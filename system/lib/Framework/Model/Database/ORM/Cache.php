<?php
namespace Model\Database\ORM;

use Model\Database\Model\TableReferenceInstance;
use Model\Database\DBAL\Fetch;

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
	static $data = null;
	static $changed = false;
	
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
	static function get($table){
		if(self::$data === null)
			self::init();
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
	static function set($table, ModelData $orm){
		$table = self::key($table);
		if($orm instanceof Model){
			//Dumb down the class, we dont need to store any additional data
			$orm = $orm->toModelData();
		}
		self::$data[$table] = $orm;
		self::$changed = true;
	}
	
	
	private static $key;
	static function init(){
		global $_SQL;
		$cfile = '/tmp/'.$_SQL->db;
		if(file_exists($cfile) && filemtime($cfile) >= (time() - 30)){
			self::$key = file_get_contents($cfile);
		}else{
			touch($cfile);
			$sql = 'SELECT MAX(UNIX_TIMESTAMP( CREATE_TIME )) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = "'.$_SQL->db.'"';
			self::$key = \DB::Q($sql)->Fetch(Fetch::FIRST);
			file_put_contents($cfile, self::$key);
		}
		self::$data = apc_fetch($_SQL->db.'_'.self::$key);
		if(!is_array(self::$data))
			self::$data = array();
		register_shutdown_function(function(){
			Cache::save();
		});
	}
	static function save(){
		global $_SQL;
		if(self::$changed){
			apc_store($_SQL->db.'_'.self::$key, self::$data);
		}
	}
}
