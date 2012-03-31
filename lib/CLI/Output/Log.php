<?php
namespace CLI\Output;

class Log {
	const LOG_PATH = '../../../../log/';
	const LOG_EXT = '.log';
	
	static $main;
	static $group;
	
	static function Init($name){		
		self::$main = self::Create($name);
	}
	static function getPath(){
		$path = __DIR__.'/'.self::LOG_PATH.'/';
		@mkdir($path);
		return $path;
	}
	static function CreateGroup($name){
		self::$group = $name;
		$file = static::getPath().self::$group.'/';
		if(!file_exists($file)){
			@mkdir($file);
		}
		self::$main = self::Create();
	}
	static function Create($name = 'main'){
		$file = static::getPath();
		if(self::$group){
			$file .= self::$group.'/';
		}
		$file .= $name;
		$file .= self::LOG_EXT;
		
		$ret = new LogFile($file);
		return $ret;
	}
	
	static function Get(){
		if(!self::$main){
			return self::Create();
		}
		return self::$main;
	}
	
	static function End(){
		self::$group = null;
	}
}