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
		return igbinary_unserialize($s);
	}

	static function set($key, $data){
		self::init();
		$data = igbinary_serialize($data);

		$res = self::$redis->set($key, $data);
		if(!$res){
			throw new \Exception("Failed to set key, error: ".self::$redis->getLastError());
		}
	}
}