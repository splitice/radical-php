<?php
use \Basic\ArrayLib\Object\SortedCollectionObject;

//psudeo Class
class ClassLoader extends Autoloader {
	static function toPath($class,$full = false){
		if($full){
			return AutoLoader::resolve($class);
		}
		return str_replace('\\', DIRECTORY_SEPARATOR, $class);
	}
	
	static function toClass($path){
		return str_replace(DIRECTORY_SEPARATOR, '\\', $path);
	}
	
	static function getProjectSpace($v){
		global $_PROJECT;
		return '\\'.$_PROJECT.'\\'.ltrim($v,'\\');
	}
	
	static function getNSExpression($expr){
		$ret = array();
	
		$path_expr = static::toPath($expr).'.php';
	
		$paths = static::$instance->getPathCache();
		foreach($paths as $p){
			foreach(glob($p.$path_expr) as $file){
				$key = static::toClass($file);
	
				$key = substr($key,strlen($p),-4);
	
				$ret[$key] = true;
			}
		}
	
		return array_keys($ret);
	}
	
	static function getAllClass(){
		$classes = array();
		$prefixLen = strlen(AutoLoader::$libDir);
		foreach(\Folder::ListDir(AutoLoader::$libDir,true) as $f){
			$f = substr($f,$prefixLen);
			$base = basename($f);
			if(substr($base,0,2) != '__' && $base != 'bootloader.php'){
				$f = ltrim($f,DIRECTORY_SEPARATOR);
				$f = strrev(dirname(strrev($f)));
				if(pathinfo($f,PATHINFO_EXTENSION) == 'php'){
					$f = substr($f,0,-4);
					$classes[] = self::toClass($f);
				}
			}
		}
		return array_unique($classes);
	}
	
	static function getVars(){
		$vars = new SortedCollectionObject(function($a,$b){
			$a = strlen($a);
			$b = strlen($b);
			
			if($a == $b) return 0;
			if($a > $b) return -1;
			return 1;
		});
		$vars['libDir'] = AutoLoader::$libDir;
		$vars['baseDir'] = AutoLoader::$baseDir;
		$vars['webDir'] = AutoLoader::$webDir;
		
		return $vars;
	}
	
	static function pathVariblize($path){
		//Prepare
		$vars = static::getVars();
		$path = realpath($path);

		$path = new File\Instance($path);
		return $path->compact($vars);
	}
	
	static function getLibraries(){
		$ret = array();
		foreach(parent::$instance->getPathCache() as $pc){
			$ret[basename($pc)] = $pc;
		}
		return $ret;
	}
}