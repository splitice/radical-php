<?php
namespace Core;

class ObjectReference extends Object {
	protected $class;
	
	function getDependencies(){
		$dependencies = static::$__dependencies;
		$class = new \Debug\PHPClassTools(\ClassLoader::toPath($this->class,true));
		foreach($class->getDependencies() as $d){
			$dependencies[] = 'php.'.str_replace('\\','.',ltrim($d,'\\'));
		}
		return $dependencies;
	}
	
	function __construct($class){
		$this->class = $class;
	}
}