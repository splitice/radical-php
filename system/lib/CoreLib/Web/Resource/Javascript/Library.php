<?php
namespace Web\Resource\Javascript;

use Core\Libraries;
use Utility\HTML\Tag;

class Library extends Tag\Script {
	static $__dependencies = array('interface.HTML.Javascript.IJavascriptLibrary');
	
	function __construct($library,$version = null){
		$class = '\\HTML\\Javascript\\Libraries\\'.$library;
		if(!class_exists($class)){
			throw new \Exception('Cant find javascript library');
		}
		$this->attributes['src'] = new $class($version);
	}
	
	
	static function Find($library,$version = null){
		$library = strtolower($library);
		$libs = Libraries::getNSExpression('HTML\\Javascript\\Libraries\\*');
		foreach($libs as $l){
			$ll = strtolower(array_pop(explode('\\',$l)));
			if($ll == $library){
				return new $l($version);
			}
		}
	}
}