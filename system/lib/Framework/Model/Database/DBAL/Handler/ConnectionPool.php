<?php
namespace Model\Database\DBAL\Handler;
use Model\Database\DBAL\Adapter\IConnection;

class ConnectionPool {
	public $pool = array();
	
	function free(IConnection $connection){
		$this->pool[] = $connection;
	}
	
	function getInstanceIWish(IConnection $connection){
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
	
	function getInstance(IConnection $connection){
		$connectionString = (string)$connection;
		if(isset($this->pool[$connectionString])){
			return $this->pool[$connectionString];
		}
		
		$connection = $connection->toInstance();
		$this->pool[$connectionString] = $connection;;
		return $connection;
	}
	
	function getAdapter($adapter){
		foreach($this->pool as $p){
			if(oneof($p,$adapter))
				return $p;
		}
	}
	
	function closeAll(){
		foreach($this->pool as $connection){
			if($connection instanceof IConnection){
				$connection->Close();
			}
		}
		$this->pool = array();
	}
}