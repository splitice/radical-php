<?php
namespace Model\Database\ORM;

use Model\Database\Model\TableReferenceInstance;

/**
 * Global cache of table ORMs
 * 
 * ORM details are decently expensive to compute so we dont
 * want to be recreating them for each table row reference (object).
 * 
 * @author SplitIce
 *
 */
class Cache {
	static $data = array();
	
	/**
	 * Resolves the $table parameter to a scalar key
	 * 
	 * @param string|TableReferenceInstance $table
	 * @return string
	 */
	private static function key($table){
		if($table instanceof TableReferenceInstance){
			//We only need the class name as our key
			return $table->getClass();
		}elseif(!is_string($table)){
			throw new \Exception('Invalid key specified');
		}
		return $table;
	}
	
	/**
	 * Get a table specific ORM from the cache
	 * 
	 * @param string|TableReferenceInstance $table the key
	 * @return ModelData
	 */
	static function Get($table){
		$table = self::key($table);
		if(isset(self::$data[$table])){
			return self::$data[$table];
		}
	}
	
	/**
	 * Set a table specific ORM from the cache
	 *
	 * @param string|TableReferenceInstance $table the key
	 * @param ModelData $orm the value to store
	 */
	static function Set($table, ModelData $orm){
		$table = self::key($table);
		if($orm instanceof Model){
			//Dumb down the class, we dont need to store any additional data
			$orm = $orm->toModelData();
		}
		self::$data[$table] = $orm;
	}
}