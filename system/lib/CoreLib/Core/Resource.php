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
		$this->dirs = array_reverse($dirs);
	}
	
	private $fullPath;
	function getFullPath(){
		if($this->fullPath) return $this->fullPath;
		
		global $BASEPATH;
		
		foreach($this->dirs as $dir){
			$file = $BASEPATH.$dir.DS.$this->path;
			if(file_exists($file)){
				$this->fullPath = $file;
				return $file;
			}
		}
	}
	function getFile(){
		return new \File\Instance($this->getFullPath());
	}
}