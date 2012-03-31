<?php
namespace Database\ORM;

use Database\Model\TableReferenceInstance;

class Cache {
	static $data = array();
	
	static function Get($table){
		if($table instanceof TableReferenceInstance){
			$table = $table->getClass();
		}
		if(isset(self::$data[$table])){
			return self::$data[$table];
		}
	}
	static function Set($table, ModelData $orm){
		if($table instanceof TableReferenceInstance){
			$table = $table->getClass();
		}
		if($orm instanceof Model){
			
		}
		self::$data[$table] = $orm;
	}
}