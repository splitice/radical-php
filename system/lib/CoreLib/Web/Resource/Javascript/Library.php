<?php
namespace Web\Resource\Javascript;

use Core\Libraries;
use Utility\HTML\Tag;

class Library extends Tag\Script {
	static $__dependencies = array('interface.HTML.Javascript.IJavascriptLibrary');
	
	function __construct($library,$version = null){
		if(substr($library,0,6) == 'local:'){
			$this->attributes['src'] = substr($library,6);
		}else{
			$class = 'Web\\Resource\\Javascript\\Libraries\\'.$library;
			if(!class_exists($class)){
				throw new \Exception('Cant find javascript library');
			}
			$this->attributes['src'] = new $class($version);
		}
	}
	
	
	static function Find($library,$version = null){
		$library = strtolower($library);
		$libs = Libraries::get('Web\\Resource\\Javascript\\Libraries\\*');
		foreach($libs as $l){
			$ll = strtolower(array_pop(explode('\\',$l)));
			if($ll == $library){
				return new $l($version);
			}
		}
	}
}