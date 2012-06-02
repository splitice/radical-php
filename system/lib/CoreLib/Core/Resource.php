<?php
namespace Core;

class Resource {
	static $default_dirs = array('system','app');
	
	private $path;
	private $dirs = array();
	function __construct($path,$dirs = null){
		$this->path = $path;
		if($dirs === null){
			$dirs = static::$default_dirs;
		}
		$this->dirs = $dirs;
	}
	
	private $fullPath;
	function getFullPath(){
		if($this->fullPath) return $this->fullPath;
		
		global $BASEPATH;
		
		foreach(array_reverse($this->dirs) as $dir){
			$file = $BASEPATH.$dir.DS.$this->path;
			if(file_exists($file)){
				$this->fullPath = $file;
				return $file;
			}
		}
	}
	function getFiles($expr = '*'){
		global $BASEPATH;
		
		$files = array();
		foreach($this->dirs as $dir){
			$file = $BASEPATH.$dir.DS.$this->path;
			if(file_exists($file) && is_dir($file)){
				$files = array_merge($files,glob($file.DIRECTORY_SEPARATOR.$expr));
			}
		}
		
		return $files;
	}
	function getFile(){
		return new \File($this->getFullPath());
	}
}