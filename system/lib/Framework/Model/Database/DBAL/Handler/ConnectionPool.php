<?php
namespace Model\Database\DBAL\Handler;
use Model\Database\DBAL\Adapter\Connection;

class ConnectionPool {
	public $pool = array();
	
	function free(Connection $connection){
		$this->pool[] = $connection;
	}
	
	function getInstanceIWish(Connection $connection){
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
	
	function getInstance(Connection $connection){
		$connectionString = (string)$connection;
		if(isset($this->pool[$connectionString])){
			return $this->pool[$connectionString];
		}
		
		$connection = $connection->toInstance();
		$this->pool[$connectionString] = $connection;;
		return $connection;
	}
	
	function closeAll(){
		foreach($this->pool as $connection){
			if($connection instanceof Connection){
				$connection->Close();
			}
		}
		$this->pool = array();
	}
}