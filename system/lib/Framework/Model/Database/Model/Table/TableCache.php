<?php
namespace Model\Database\Model\Table;

use Utility\Cache\Object\WeakRef;
use Model\Database\Model\Table;

class TableCache {
	const MAX_ENTRIES = 500;
	static $cache;
	
	private static function init(){
		if(!self::$cache){
			self::$cache = new WeakRef();
		}
	}
	private static function _Add($key,$value){
		self::Init();
		self::$cache->Set($key,$value);
	}
	static function Add($object){
		//Never cache for CLI
		if(php_sapi_name() == 'cli') 
			return $object;
		
		if($object instanceof TableSet){
			self::_Add($object->sql,$object);
		}elseif($object instanceof Table){
			self::_Add($object->getIdKey(),$object);
		}else{
			throw new \Exception('Couldnt add the object to TableCache, object is an instance of '.get_class($object));
		}
		if(self::$cache->count() > self::MAX_ENTRIES){
			self::$cache->gc(true);
		}
		return $object;
	}
	static function get($key){
		self::Init();
		return self::$cache->Get($key);
	}
}