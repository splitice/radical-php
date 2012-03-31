<?php
namespace Database\Model;

class TableReference extends \Core\Object {	
	protected static function getClasses(){
		return \ClassLoader::getNSExpression(\ClassLoader::getProjectSpace('DB\\*'));
	}
	
	private static $_name = array();
	static function getByTableName($tableName){
		if(!self::$_name){
			foreach(static::getClasses() as $class){
				self::$_name[$class::TABLE] = $class;
			}
		}
		if(isset(self::$_name[$tableName])){
			return static::_create(self::$_name[$tableName]);
		}
	}
	protected static function _create($class){
		$c = get_called_class().'Instance';
		return new $c($class);
	}
	static function getAll(){
		$ret = array();
		foreach(static::getClasses() as $class){
			$ret[] = static::_create($class);
		}
		return $ret;
	}
	
	static function getByTableClass($tableClass){
		try {
			return static::_create($tableClass);
		}catch(\Exception $ex){
			
		}
	}
}