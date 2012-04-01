<?php
namespace Database\DBAL\Handler;
use Database\DBAL\Adapter\Connection;

class ConnectionPool {
	public $pool = array();
	
	function Free(Connection $connection){
		$this->pool[] = $connection;
	}
	
	function GetInstanceIWish(Connection $connection){
		$connectionString = (string)$connection;
		foreach($this->pool as $k=>$p){
			if(((string)$p) == $connectionString){
				unset($this->pool[$k]);
				//echo "Recycled Connection\r\n";
				return $p;
			}
		}
		//echo "New Connection\r\n";
		return $connection->toInstance();
	}
	
	function GetInstance(Connection $connection){
		$connectionString = (string)$connection;
		if(isset($this->pool[$connectionString])){
			return $this->pool[$connectionString];
		}
		
		$connection = $connection->toInstance();
		$this->pool[$connectionString] = $connection;;
		return $connection;
	}
	
	function CloseAll(){
		foreach($this->pool as $connection){
			if($connection instanceof Connection){
				$connection->Close();
			}
		}
		$this->pool = array();
	}
}