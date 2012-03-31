<?php
namespace Database\DBAL\Adapter;
use Database\Exception;

class Connection {
	/**
	 * @var \mysqli
	 */
	private $mysqli;
	
	private $host;
	private $user;
	private $pass;
	private $db;
	private $port;
	private $compression;
	
	function __construct($host, $user, $pass, $db = null, $port = 3306, $compression=true){
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->db = $db;
		$this->port = $port;
		$this->compression = $compression;
	}
	
	/**
	 * Connect to database
	 * 
	 * @throws Exception\ConnectionException
	 * @return \mysqli
	 */
	function Connect(){
		if($this->isConnected()){
			return $this->mysqli;
		}
		
		$this->mysqli = mysqli_init();
		
		//Connect - With compression
		$connection_status = mysqli_real_connect ( $this->mysqli, $this->host, 
				$this->user, $this->pass, $this->db, $this->port,
				null, $this->compression?MYSQLI_CLIENT_COMPRESS:0 );
		
		if (! $connection_status) {
			throw new Exception\ConnectionException ( $this->__toString(), $this->Error() );
		}
		
		return $this->mysqli;
	}
	
	function Ping(\mysqli $mysqli=null){
		if(!$mysqli){
			$mysqli = $this->Connect();
		}

		//Ping
		return $mysqli->ping();		
	}
	
	/**
	 * is the MySQL server connected?
	 * @return boolean
	 */
	private $_connectCache;
	private $_connectHit;
	function isConnected() {
		$ret = false;
		if($this->_connectHit >= ($t = time())){
			if($this->_connectCache == \CLI\Thread::$self){
				$ret = true;
			}
		}
		if(!$ret){
			$ret = ($this->mysqli && $this->mysqli->ping());
		}
		if($ret){
			$this->_connectCache = \CLI\Thread::$self;
			$this->_connectHit = $t+30;//Persume we can hold a connection for 30s
		}
		if(memory_get_usage()>(1024*1024*500)){
			ob_start();
			debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
			file_put_contents('/test.txt', ob_get_contents());
			exit;
		}
		
		return $ret;
	}
	
	function toInstance(){
		return new Instance($this->host,$this->user,$this->pass,$this->db,$this->port,$this->compression);
	}
	
	function reConnect(){
		$this->Close();
		$this->Connect();
	}
	
	function Close(){
		if($this->mysqli){
			mysqli_close($this->mysqli);
			$this->mysqli = null;
		}
	}
	
	function __destruct(){
		//I wish I could implement it this way		
		//echo "Connection Freed\r\n";
		//\DB::$connectionPool->Free($this);
	}
	
	function Query($sql){
		return $this->Connect()->query ( $sql );
	}
	
	function Escape($string){
		return $this->Connect()->real_escape_string($string);
	}
	
	/**
	 * Return the last MySQL error
	 */
	function Error() {
		return $this->mysqli->error;
	}
	
	/**
	 * @return string
	 */
	function __toString(){
		return 'mysqli://' . $this->user . '@' . $this->host . ':' . $this->port . ($this->compression?'z':'') . '/' . $this->db;
	}
	
	static function fromArray(array $from){
		if(!isset($from['host'])){
			throw new \InvalidArgumentException('Mysql connection parameters must have a host');
		}
		if(!isset($from['user'])){
			throw new \InvalidArgumentException('Mysql connection parameters must have a username');
		}
		if(!isset($from['pass'])){
			throw new \InvalidArgumentException('Mysql connection parameters must have a password');
		}
		if(!isset($from['db'])){
			throw new \InvalidArgumentException('Mysql connection parameters must have a database');
		}
		if(!isset($from['port'])){
			$from['port'] = 3306;
		}
		if(!isset($from['compression'])){
			$from['compression'] = false;
		}
		return new static($from['host'],$from['user'],$from['pass'],$from['db'],$from['port'],$from['compression']);
	}
}