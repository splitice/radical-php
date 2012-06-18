<?php
namespace Core;
use Basic\Arr\Object\SortedCollectionObject;

class Path {
	/**
	 * Get variables for the project
	 * 
	 * @return array
	 */
	static function getVars(){
		$vars = new SortedCollectionObject(function($a,$b){
			$a = strlen($a);
			$b = strlen($b);
				
			if($a == $b) return 0;
			if($a > $b) return -1;
			return 1;
		});
		
		global $BASEPATH;
		$vars['projectBase'] = $BASEPATH;
	
		return $vars;
	}
	
	/**
	 * Convert a full filesystem path to one relative to project variables.
	 * This is commonly used in error reports to remove full paths from the output.
	 * 
	 * @param string $path
	 * @return string
	 */
	static function pathVariblize($path){
		//Prepare
		$vars = static::getVars();
		$path = realpath($path);
	
		//Create a file object
		$path = new \File($path);
		
		//Variabilize
		return $path->compact($vars);
	}
}