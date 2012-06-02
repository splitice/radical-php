<?php
namespace Core;
use \Basic\ArrayLib\Object\SortedCollectionObject;

class Path {
	static function getVars(){
		$vars = new SortedCollectionObject(function($a,$b){
			$a = strlen($a);
			$b = strlen($b);
				
			if($a == $b) return 0;
			if($a > $b) return -1;
			return 1;
		});
		//$vars['projectDir'] = AutoLoader::$;
		$vars['baseDir'] = AutoLoader::$baseDir;
	
		return $vars;
	}
	
	static function pathVariblize($path){
		//Prepare
		$vars = static::getVars();
		$path = realpath($path);
	
		$path = new File\Instance($path);
		return $path->compact($vars);
	}
}