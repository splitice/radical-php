<?php
namespace HTML\Javascript;
use HTML\Tag;

class Library extends Tag\Script {
	static $__dependencies = array('interface.HTML.Javascript.IJavascriptLibrary');
	
	function __construct($library,$version = null){
		$class = '\\HTML\\Javascript\\Libraries\\'.$library;
		if(!class_exists($class)){
			throw new \Exception('Cant find javascript library');
		}
		$this->attributes['src'] = new $class($version);
	}
}