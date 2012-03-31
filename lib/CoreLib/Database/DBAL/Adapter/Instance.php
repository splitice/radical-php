<?php
namespace Database\DBAL\Adapter;
use Database\IToSQL;
use Database\SQL;
use Basic\Weakref\Callback as WeakrefCallback;
use Database\DBAL;
use Database\Exception;

class Instance extends Connection {
	const QUERY_TIMEOUT = 30;
	
	/* Psudeo Returns */
	const NOT_A_RESULT = null;
	
	
	function __construct($host, $user, $pass, $db = null, $port = 3306, $compression=true){
		//register_shutdown_function(WeakrefCallback::Callback($this,'_exit'));
		parent::__construct($host, $user, $pass, $db, $port, $compression);
	}
	
	function Close(){
		\DB::$connectionPool->Free($this);
	}
	
	/**
	 * @var \mysqli
	 */
	public $isInQuery = false;
	
	function _exit(){
		if($this->isInQuery){
			//Display secific error page?
		}
	}
	
	/**
	 * Return the number of affected rows of the last MySQL query
	 */
	function AffectedRows() {
		return mysqli_affected_rows ( $this->Connect() );
	}
	
	/**
	 * Execute MySQL query
	 * @param string $sql
	 * @throws \DB\Exception\QueryError
	 * @return resource|NOT_A_RESULT
	 */
	function Query($sql,$timeout=self::QUERY_TIMEOUT,$is_retry=false) {
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
		$res = parent::Query($sql);
		
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
				throw new Exception\QueryError ( $sql, $this->Error () );
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
	function Q($sql,$timeout=self::QUERY_TIMEOUT){
		return $this->Query($sql,$timeout);
	}
	
	function MultipleInsert($tbl, $cols, $data, $ignore = false){
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
	function Insert($tbl, $data, $ignore = false) {
		$insert = new SQL\InsertStatement($tbl, $data, $ignore);
		//die(var_dump($insert->toSQL()));
		//Execute
		return ( bool ) $this->Query ( $insert );
	}
	
	/**
	 * Build and Execute a MySQL update
	 * @param string $tbl table name
	 * @param array $data unescaped data in key=>value format
	 * @param array $where Where conditions
	 * @return boolean
	 */
	function Update($tbl, $data, $where) {
		$update = new SQL\UpdateStatement($tbl, $data, $where);
		
		//Execute
		return ( bool ) $this->Query ( $update );
	}
	
	function FOUND_ROWS() {
		$res = $this->Query ( 'SELECT FOUND_ROWS()' );
		return $this->Fetch ( $res, DBAL\Fetch::FIRST );
	}
	
	/**
	 * Delete row[s] from a mysql database
	 * @param string $tbl table name
	 * @param string $where where condition
	 */
	function Delete($tbl, $where) {
		$delete = new SQL\DeleteStatement($tbl, $where);
		$this->Query ( $delete );
	}
	
	function TableExists($table){
		$sql = 'show tables like '.\DB::E($table);
		$res = \DB::Q($sql);
		if(\DB::Fetch($res)) return true;
		return false;
	}
	
	/**
	 * Escape a value into SQL format
	 * @param string|int $str
	 * @return string
	 */
	function Escape($str) {
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
			if(method_exists($str, 'toEscaped')){
				return $str->toEscaped();
			}else{
				throw new \BadMethodCallException('cant escape this object, non escapable');
			}
		}
		return '\'' . parent::Escape ( $str ) . '\'';
	}
	
	function E($str){
		return $this->Escape($str);
	}
	
	/**
	 * Return the AUTO_INCREMENT value of the last MySQL insert
	 */
	function InsertId() {
		return mysqli_insert_id ( $this->Connect() );
	}
	
	function Fetch(DBAL\Result $res, $format = DBAL\Fetch::ASSOC, $cast=null){
		return $res->Fetch($format,$cast);
	}
	
	/**
	 * Perform MySQL fetch and execute $callback on it returning the result
	 * @param mysqli_result $res
	 * @param function $callback
	 * @param DB\Fetch:: $format
	 * @return Array <int, mixed>
	 */
	function FetchCallback($res, $callback, $format = DBAL\Fetch::ALL_ASSOC) {
		return $res->FetchCallback($callback,$format);
	}
	
	
	function numRows($res){
		return mysqli_num_rows($res);
	}
	
	/* Start / End transaction */
	function TransactionStart(){
		$this->Query('START TRANSACTION');
	}
	function TransactionCommit(){
		$this->Query('COMMIT');
	}
}