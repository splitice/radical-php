<?php
namespace DDL\TitleParse\Internal;

abstract class TitleParseBase extends TitleResources {
	protected $rls;
	protected $valid = true;
	
	const NATIVE_TYPE = null;
	
	function __construct($str){
		$this->rls = trim($str);
	}
	static function getNType(){
		return static::NATIVE_TYPE;
	}
	function isValid($b=null){
		if($b!==null){
			$this->valid = (bool)$b;
		}
		return $this->valid;
	}
	/* Depreciated */
	function setValid($b){
		$this->valid = $b;
	}
	
	function has($method){
		return method_exists($this,$method);
	}
	
	static function is($string){
		$c = get_called_class();
		$obj = new $c($string);
		return $obj->isValid();
	}
}