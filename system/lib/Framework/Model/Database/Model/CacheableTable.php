<?php
namespace Model\Database\Model;

class CacheableTable extends Table {
	private $related_cache = array();
	function _related_cache($name,$o){
		$this->related_cache[$name] = $o;
		return $o;
	}
	function _related_cache_get($name){
		return isset($this->related_cache[$name])?$this->related_cache[$name]:null;
	}
	
	static function _idString($id){
		if(is_object($id)){
			$id = (array)$id;
		}
		if(is_array($id)) {
			ksort($id);
			$id = implode('|',$id);
		}
		$id .= '|'.get_called_class();
		return $id;
	}
	
	public function getIdKey(){
		return static::_idString($this->getId());
	}
	
	static function fromId($id){
		//Check Cache
		$cache_string = static::_idString($id);
		$ret = Table\TableCache::Get($cache_string);
	
		//If is cached
		if($ret){
			return $ret;
		}
		
		$ret = parent::fromId($id);
		if($ret)
			Table\TableCache::Add($ret);
		
		return $ret;
	}
	
	static function getAll($sql = ''){
		$obj = static::_getAll($sql);
		
		$cached = Table\TableCache::Get($obj);
		if($cached){
			return $cached;
		}else{
			return new Table\CacheableTableSet($obj, get_called_class());
		}
	}
}