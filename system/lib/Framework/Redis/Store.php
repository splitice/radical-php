<?php
namespace Redis;

class Store {
	private $key;
	static $redis;
	
	function __construct($key){
		$this->key = $key;
	}
	
	static function init(){
		if(!self::$redis){
			self::$redis = new \Redis();
			if(!self::$redis->connect('127.0.0.1', 6379)){
				throw new \Exception("Could not connect to Redis server.");
			}
			
		}
	}
	
	function get(){
		self::init();
		$data = self::$redis->get($this->key);

		if($data === false)
			return null;
		
		return igbinary_unserialize($data);
	}
	
	function set($data){
		self::init();
		$data = igbinary_serialize($data);
		
		$res = self::$redis->set($this->key, $data);
		if(!$res){
			throw new \Exception("Failed to set key, error: ".self::$redis->getLastError());	
		}
	}
}