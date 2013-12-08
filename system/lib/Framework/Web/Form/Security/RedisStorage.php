<?php
namespace Web\Form\Security;

class RedisStorage {
	static $redis;

	static function init(){
		if(!self::$redis){
			self::$redis = new \Redis();
			if(!self::$redis->connect('127.0.0.1', 6379)){
				throw new \Exception("Could not connect to Redis server.");
			}
				
		}
	}

	static function get($key){
		self::init();
		$s = self::$redis->get($key);
		
		$e2 = @gzinflate($s);
		if(empty($e2) || $e2 === false){
			$e2 = $s;
		}
		return igbinary_unserialize($e2);
	}

	static function set($key, $data){
		self::init();
		$data = igbinary_serialize($data);
		$data = gzdeflate($data, 9);
		$res = self::$redis->set($key, $data, 3600);
		
		/*$s = self::$redis->get($key);
		$r = igbinary_unserialize($s);
		die(var_dump($r));*/
		if(!$res){
			throw new \Exception("Failed to set key, error: ".self::$redis->getLastError());
		}
	}
}