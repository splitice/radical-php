<?php
namespace Core;

class Object {
	static $__dependencies = array();
	static $__provides = array();
	
	static function __getDependencies(){
		$dependencies = static::$__dependencies;
		$class = new \Debug\PHPClassTools(\ClassLoader::toPath(get_called_class(),true));
		foreach($class->getDependencies() as $d){
			$dependencies[] = 'php.'.str_replace('\\','.',ltrim($d,'\\'));
		}
		return $dependencies;
	}
	
	static function __getProvides(){
		$provides = static::$__provides;
		$provides[] = 'php.'.str_replace('\\', '.', $provides);
		return $provides;
	}
}