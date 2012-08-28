<?php
namespace Utility\DDL;

class Host {
	protected $id;
	function __construct($id){
		$this->id = $id;
	}
	
	function upload(){
		$class = '\\Utility\\DDL\\Hosts\\Upload\\'.$this->id;
		if(class_exists($class)){
			return new $class();
		}
	}
	
	function check(){
		$class = '\\Utility\\DDL\\Hosts\\Check\\'.$this->id;
		if(class_exists($class)){
			return new $class();
		}
	}
	
	static function fromID($id){
		$b = '\\Utility\\DDL\\Hosts\\';
		if(!class_exists($b.'Upload\\'.$id) || !class_exists($b.'Check\\'.$id)){
			return false;
		}
		return new static($id);
	}
	static function getAll(){
		$ret = array();
		foreach(glob(__DIR__.DS.'Hosts'.DS.'*'.DS.'*.php') as $v){
			$class = substr(basename($v),0,-4);
			$ret[] = new static($class);
		}
		return $ret;
	}
}