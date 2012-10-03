<?php
namespace Model\Database\DBAL\Adapter;
use Model\Database\DBAL\Instance;

use Model\Database\Exception;

class SQLiteConnection implements IConnection {
	/**
	 * @var \SQLite3
	 */
	private $sqlite;
	
	private $file;
	
	function __construct($file){
		$this->file = $file;
	}
	
	/**
	 * Connect to database
	 * 
	 * @throws Exception\ConnectionException
	 * @return \SQLite3
	 */
	function connect(){
		if($this->isConnected()){
			return $this->mysqli;
		}
		
		try {
			$this->sqlite = new \SQLite3($this->file);
		}
		catch(\Exception $ex){
			throw new Exception\ConnectionException ( $this->__toString(), $ex->getMessage() );
		}
		
		return $this->sqlite;
	}
	
	function ping(){
		return true;	
	}
	
	function isConnected() {
		return (bool)$this->sqlite;
	}
	
	function toInstance(){
		return new Instance($this,$this->file);
	}
	
	function reConnect(){
		$this->Close();
		$this->Connect();
	}
	
	function close(){
		if($this->sqlite){
			sqlite_close($this->sqlite);
			$this->sqlite = null;
		}
	}
	
	function query($sql){
		$sql = trim($sql);
		if(!$sql){
			throw new \Exception('Empty Query');
		}
		
		return $this->connect()->query ( $sql );
	}
	
	function prepare($sql){
		$sql = trim($sql);
		if(!$sql){
			throw new \Exception('Empty Query');
		}
	
		return new MySQL\PreparedStatement($sql);
	}
	
	function escape($string){
		return $this->Connect()->escapeString($string);
	}
	
	/**
	 * Return the last MySQL error
	 */
	function error() {
		if($this->sqlite === null)
			return null;
		
		return $this->sqlite->lastErrorMsg();
	}
	
	/**
	 * Return the number of affected rows of the last MySQL query
	 */
	function affectedRows() {
		return null;
	}
	
	/**
	 * @return string
	 */
	function __toString(){
		return 'sqlite://' . $this->file;
	}
	
	static function fromArray(array $from){
		if(!isset($from['file'])){
			throw new \InvalidArgumentException('Sqlite connection parameters must have a file to load');
		}
		return new static($from['file']);
	}
}