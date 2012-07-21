<?php
namespace Web\Resource\CSS;
use Utility\HTML\Tag;

class Library extends Tag\Link {
	static $__dependencies = array('interface.HTML.CSS.ICSSLibrary');
	
	function __construct($library,$version = null){
		$class = 'Web\\Resource\\CSS\\Libraries\\'.$library;
		if(!class_exists($class)){
			throw new \Exception('Cant find css library');
		}
		$this->attributes['href'] = new $class($version);
	}
}