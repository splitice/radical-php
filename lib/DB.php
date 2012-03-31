<?php
use Database\DBAL\Adapter;
use Database\DBAL;
use Database\DBAL\Handler;

/**
 * Database Interface Class
 * @author SplitIce
 *
 */
class DB extends Adapter\SQLUtils {	
	static $connectionPool;

	static $query_log;
	
	static $connectionDetails;
	
	static $isInQuery = false;
	
	function __construct(){
		throw new \BadMethodCallException('DB is an abstract class');
	}
	
	static function Init(){
		if(!static::$query_log){
			static::$query_log = new Handler\QueryLog ();
		}
		
		if(!static::$connectionPool){
			static::$connectionPool = new Handler\ConnectionPool();
		}
	}
	
	/**
	 * Connect to a MySQL database
	 * @param string $host
	 * @param string $user
	 * @param string $pass
	 * @param string $db
	 * @throws \Database\Exception\ConnectionException
	 */
	static function Connect(Adapter\Connection $connection) {
		static::Init();
		
		static::$connectionDetails = $connection;
		
		return static::getInstance($connection);
	}
	
	/**
	 * @param Database\Connection $connection
	 * @return DB
	 */
	static function getInstance(Adapter\Connection $connection = null){
		if($connection === null){
			if(static::$connectionDetails === null){
				global $_SQL;
				if(isset($_SQL)){
					static::Connect($_SQL);
				}
			}
			$connection = static::$connectionDetails;
		}
		
		if(!static::$connectionPool){
			throw new Database\Exception\ConnectionException('');
		}
		
		//Get Database Instance from connection details
		return static::$connectionPool->GetInstance($connection);
	}

	static function reConnect(){
		static::getInstance()->Close();
		static::getInstance()->Connect();
	}
	
	static function MultiQuery(){
		return new DBAL\MultiQuery(self::$con);
	}
	

	/**
	 * Convert MySQL timestamp to php integer timestamp
	 * @param string $d
	 * @return number
	 */
	static function TimeStamp($d) {
		return strtotime ( $d );
	}

	static function toTimeStamp($i) {
		return date ( "Y-m-d H:i:s", $i );
	}

	static function BIN($x) {
		return new DBAL\Binary($s);
	}

	public static function __callStatic($method,$argument){
		$instance = static::getInstance();
		if(!method_exists($instance,$method)){
			throw new \BadMethodCallException('Database method: "'.$method.'" doesnt exist');
		}
		$a = count($argument);
		if($a === 1){
			return $instance->$method($argument[0]);
		}
		
		return call_user_func_array(array($instance,$method),$argument);
	}
	
	/* Predefined Methods */
	
	static function Close(){
		//Close all connections
	}
	
	static function TableExists() {
		return static::__callStatic(__FUNCTION__, func_get_args());
	}
	
	/**
	 * Return the number of affected rows of the last MySQL query
	 */
	static function AffectedRows() {
		return static::__callStatic(__FUNCTION__, func_get_args());
	}
	
	/**
	 * Execute MySQL query
	 * @param string $sql
	 * @throws \Database\Exception\QueryError
	 * @return \Database\DBAL\Result
	 */
	static function Query($sql,$timeout=Adapter\Instance::QUERY_TIMEOUT,$is_retry=false) {
		return static::__callStatic(__FUNCTION__, func_get_args());
	}
	
	/**
	 * Shorthand for Query
	 *
	 * @param string $sql
	 * @param int $timeout
	 * @return Ambigous <resource, \Database\NOT_A_RESULT, string, unknown>
	 */
	static function Q($sql,$timeout=Adapter\Instance::QUERY_TIMEOUT){
		return static::__callStatic(__FUNCTION__, func_get_args());
	}
	
	static function MultipleInsert($tbl, $cols, $data, $ignore = false){
		return static::__callStatic(__FUNCTION__, func_get_args());
	}
	
	/**
	 * Build and Execute a MySQL insert
	 * @param string $tbl table name
	 * @param string $data unescaped data in key=>value format
	 * @return boolean
	 */
	static function Insert($tbl, $data, $ignore = false) {
		return static::__callStatic(__FUNCTION__, func_get_args());
	}
	
	/**
	 * Build and Execute a MySQL update
	 * @param string $tbl table name
	 * @param array $data unescaped data in key=>value format
	 * @param array $where Where conditions
	 * @return boolean
	 */
	static function Update($tbl, $data, $where) {
		return static::__callStatic(__FUNCTION__, func_get_args());
	}
	
	static function FOUND_ROWS() {
		return static::__callStatic(__FUNCTION__, func_get_args());
	}
	
	/**
	 * Delete row[s] from a mysql database
	 * @param string $tbl table name
	 * @param string $where where condition
	 */
	static function Delete($tbl, $where) {
		return static::__callStatic(__FUNCTION__, func_get_args());
	}
	
	/**
	 * Escape a value into SQL format
	 * @param string|int $str
	 * @return string
	 */
	static function Escape($str) {
		return static::__callStatic(__FUNCTION__, func_get_args());
	}
	
	static function E($str){
		return static::__callStatic(__FUNCTION__, func_get_args());
	}
	
	/**
	 * Return the last MySQL error
	 */
	static function Error() {
		return static::__callStatic(__FUNCTION__, func_get_args());
	}
	
	/**
	 * Return the AUTO_INCREMENT value of the last MySQL insert
	 */
	static function InsertId() {
		return static::__callStatic(__FUNCTION__, func_get_args());
	}
	
	static function Fetch(DBAL\Result $res, $format = DBAL\Fetch::ASSOC, $cast=null){
		return static::__callStatic(__FUNCTION__, func_get_args());
	}
	
	/**
	 * Perform MySQL fetch and execute $callback on it returning the result
	 * @param mysqli_result $res
	 * @param function $callback
	 * @param Database\Fetch:: $format
	 * @return Array <int, mixed>
	 */
	static function FetchCallback($res, $callback, $format = DBAL\Fetch::ALL_ASSOC) {
		return static::__callStatic(__FUNCTION__, func_get_args());
	}
	
	
	static function numRows($res){
		return static::__callStatic(__FUNCTION__, func_get_args());
	}
	
	/* Start / End transaction */
	static function TransactionStart(){
		return static::__callStatic(__FUNCTION__, func_get_args());
	}
	static function TransactionCommit(){
		return static::__callStatic(__FUNCTION__, func_get_args());
	}
}