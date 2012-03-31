<?php
namespace Database\Model\Table;

class Relations {
	static $relations = array();
	
	static function getClass($class){
		if(isset(self::$relations[$class])) return self::$relations[$class];
		return array();
	}
	
	static function setClass($class,$data){
		self::$relations[$class] = $data;
	}
}