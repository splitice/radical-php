<?php
namespace Cache;

class GlobalCache extends PooledCache {
	const POOL_NAME = 'global';
	protected static $cache = array();
	
	static function Get($object){
		return parent::Get(static::POOL_NAME,$object);
	}
}