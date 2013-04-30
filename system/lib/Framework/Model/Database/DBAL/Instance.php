<?php
namespace Model\Database\DBAL;
use Model\Database\IToSQL;
use Model\Database\SQL;
use Basic\Weakref\Callback as WeakrefCallback;
use Model\Database\DBAL;
use Model\Database\Exception;
use Model\Database\Model\TableReference;

class Instance {
	const QUERY_TIMEOUT = 30;
	
	/* Psudeo Returns */
	const NOT_A_RESULT = null;
	
	private $adapter;
	function __construct(Adapter\IConnection $adapter, $host, $user, $pass, $db = null, $port = 3306, $compression=true){
		$this->adapter = new $adapter($host, $user, $pass, $db, $port, $compression);
	}
	
	function close(){
		\DB::$connectionPool->Free($this);
	}
	
	function __call($func,$args){
		return call_user_func_array(array($this->adapter,$func), $args);
	}
	
	/**
	 * @var \mysqli
	 */
	public $isInQuery = false;
	
	/**
	 * Execute MySQL query
	 * @param string $sql
	 * @throws \DB\Exception\QueryError
	 * @return resource|NOT_A_RESULT
	 */
	function query($sql,$timeout=self::QUERY_TIMEOUT,$is_retry=false) {
		$mysqli = $this->Connect();
		
		//We are now in-query
		$this->isInQuery = $sql;
		
		//if(!\Server::isCLI()){
		//	set_time_limit($timeout);
		//}
		
		//Build SQL if applicable
		if($sql instanceof IToSQL){
			$sql = $sql->toSQL();
		}
		
		//Do Query
		//echo $sql,"\r\n";
		$res = $this->adapter->Query($sql);
		
		//Query Done
		$this->isInQuery = false;
		
		//if(!\Server::isCLI()){
		//	set_time_limit(0);
		//}
	
		if ($res === false) { //Failure
			$errno = mysqli_errno( $mysqli );
			if(!$is_retry && ($errno == 2006 || $errno == 2013)){
				$this->reConnect();
				return $this->Q($sql,$timeout,true);
			}else{
				if($errno == 1213){
					throw new TransactionException($this->Error ());
				}else{
					throw new Exception\QueryError ( $sql, $this->Error () );
				}
			}
		} else {
			\DB::$query_log->addQuery ( $sql ); //add query to log
	
			if ($res === true) { //Not a SELECT, SHOW, DESCRIBE or EXPLAIN
				return static::NOT_A_RESULT;
			} else {
				return new DBAL\Result($res,$this);
			}
		}
	}
	
	/**
	 * Shorthand for Query
	 * 
	 * @param string $sql
	 * @param int $timeout
	 * @return Ambigous <resource, \DB\NOT_A_RESULT, string, unknown>
	 */
	function q($sql,$timeout=self::QUERY_TIMEOUT){
		return $this->Query($sql,$timeout);
	}
	
	function multipleInsert($tbl, $cols, $data, $ignore = false){
		$append = array();
		foreach($data as $d){
			$append[] = '(' . $this->A ( $d ) . ')';
		}
		$append = implode(',', $append);
	
		$sql = 'INSERT ' . ($ignore ? 'IGNORE ' : '') . 'INTO `' . $tbl . '` (`' . implode ( '`,`', $cols ) . '`) VALUES'.$append;
		return ( bool ) $this->Query ( $sql );
	}
	
	/**
	 * Build and Execute a MySQL insert
	 * @param string $tbl table name
	 * @param array $data unescaped data in key=>value format
	 * @return boolean
	 */
	function insert($tbl, $data, $ignore = false) {
		$insert = new SQL\InsertStatement($tbl, $data, $ignore);

		//Execute
		$success = $this->Query ( $insert );

		if($success === false) return false;
		
		//NOT_A_RESULT
		return $this->InsertId();
	}
	
	/**
	 * Build and Execute a MySQL update
	 * @param string $tbl table name
	 * @param array $data unescaped data in key=>value format
	 * @param array $where Where conditions
	 * @return boolean
	 */
	function update($tbl, $data, $where) {
		$update = new SQL\UpdateStatement($tbl, $data, $where);
		
		//Execute
		return ( bool ) $this->Query ( $update );
	}
	
	function found_rows() {
		$res = $this->Query ( 'SELECT FOUND_ROWS()' );
		return $this->Fetch ( $res, DBAL\Fetch::FIRST );
	}
	
	/**
	 * Delete row[s] from a mysql database
	 * @param string $tbl table name
	 * @param string $where where condition
	 */
	function delete($tbl, $where) {
		$delete = new SQL\DeleteStatement($tbl, $where);
		$this->Query ( $delete );
	}
	
	function tableExists($table){
		return TableReference::getByTableClass(__CLASS)->exists();
	}
	
	/**
	 * Escape a value into SQL format
	 * @param string|int $str
	 * @return string
	 */
	function escape($str) {
		if ($str === null) {
			return 'NULL';
		}
		if(is_numeric($str) && ((int)$str) == $str){
			return $str;
		}
		if (is_array ( $str )) {
			throw new \BadMethodCallException('cant escape an array');
		}
		if (is_object ( $str )) {
			if(method_exists($str, 'toEscaped')){//depreciated
				return $str->toEscaped();
			}elseif($str instanceof IToSQL){
				return $str->toSQL();
			}elseif(method_exists($str, '__toString')){
				$str = (string)$str;
			}else{
				throw new \BadMethodCallException('cant escape this object, non escapable');
			}
		}
		return '\'' . $this->adapter->Escape ( $str ) . '\'';
	}
	
	function e($str){
		return $this->Escape($str);
	}
	
	/**
	 * Return the AUTO_INCREMENT value of the last MySQL insert
	 */
	function insertId() {
		$mysqli = $this->Connect();
		return $mysqli->insert_id;
	}
	
	function fetch(DBAL\Result $res, $format = DBAL\Fetch::ASSOC, $cast=null){
		return $res->Fetch($format,$cast);
	}
	
	/**
	 * Perform MySQL fetch and execute $callback on it returning the result
	 * @param mysqli_result $res
	 * @param function $callback
	 * @param DB\Fetch:: $format
	 * @return Array <int, mixed>
	 */
	function fetchCallback($res, $callback, $format = DBAL\Fetch::ALL_ASSOC) {
		return $res->FetchCallback($callback,$format);
	}
	
	
	function numRows($res){
		return mysqli_num_rows($res);
	}
	
	/* Start / End transaction */
	function transactionStart(){
		$this->Query('START TRANSACTION');
	}
	function transactionCommit(){
		$result = $this->adapter->commit();
		if(!$result){
			throw new TransactionException("Commit failed");
		}
	}
	function transactionRollback(){
		$this->adapter->rollback();
	}
	
	function transaction($method){
		try {
			$this->transactionStart();
			$ret = $method();
			$this->transactionCommit();
			return $ret;
		}catch(TransactionException $ex){
			$this->transactionRollback();
			return $method();
		}
	}
}