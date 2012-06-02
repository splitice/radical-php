<?php
namespace Net\ExternalInterfaces\ContentAPI;

class Recognise {
	private static function oneof($object, $class){
	    if(is_object($object)) return $object instanceof $class;
	    if(is_string($object)){
	        if(is_object($class)) $class=get_class($class);
	
	        if(class_exists($class)) return is_subclass_of($object, $class) || $object==$class;
	        if(interface_exists($class)) {
	            $reflect = new \ReflectionClass($object);
	            return $reflect->implementsInterface($class);
	        }
	
	    }
	    return false;
	} 
	private static function Classes(){
		$ret = array();
		
		$dir = __DIR__.DIRECTORY_SEPARATOR.'Modules'.DIRECTORY_SEPARATOR.'*.php';
		foreach(glob($dir) as $file){
			$class = '\\ContentAPI\\Modules\\'.basename($file,'.php');
			if(class_exists($class)){
				$ret[] = $class;
			}
		}
		return $ret;
	}
	static function URL($url){
		$ret = array();
		foreach(self::Classes() as $class){
			if(self::oneof($class,'\\ContentAPI\\Interfaces\\IFromURL')){
				if($class::RecogniseURL($url)){
					$ret[] = $class::fromURL($url);
				}
			}
		}
		return $ret;
	}
	static function RecogniseAll($data){
		return self::URL($data);//Nothing more needed
	}
}