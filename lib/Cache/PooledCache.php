<?php
namespace Cache;

class PooledCache {
	protected static $cache = array();
	
	static function Get($pool, $object){
		if(isset(static::$cache[$object])){
			return static::$cache[$object];
		}
		
		$c = 'Cache\\Object\\'.$object;
		if(!class_exists($c)){
			throw new \Exception('Cant find cache of type: '.$object);
		}
		
		$cache = new $c($pool);
		static::$cache[$object] = $cache;
		
		return $cache;
	}
}