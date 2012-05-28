<?php
namespace Database\Model\Table;
use Database\Model\Table;

class TableCache {
	const MAX_ENTRIES = 500;
	static $cache;
	
	private static function Init(){
		if(!self::$cache){
			self::$cache = new \Cache\Object\WeakRef();
		}
	}
	private static function _Add($key,$value){
		self::Init();
		self::$cache->Set($key,$value);
	}
	static function Add($object){
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
	static function Get($key){
		self::Init();
		return self::$cache->Get($key);
	}
}