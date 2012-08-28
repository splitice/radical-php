<?php
namespace Web\Page\Router\Recognisers\Templates;

use Utility\Net\URL;
use Basic\String\Format;
use Web\Page\Router\IPageRecognise;
use Web\Page\Controller;
use Web\Page\Handler;

class Standard implements IPageRecognise {
	static $match = array();
	static function recognise(URL $url){
		$path = $url->getPath()->getPath(true);
		foreach(static::$match as $expr=>$class){
			$match = Format::Consume($path, $expr);
			if($match){
				if(is_array($class) || is_string($class)){
					if(is_array($class)){
						$data = isset($class['data'])?$class['data']:$match;
						$class = $class['class'];
					}else{
						$data = $match;
					}
					if(is_string($class)){
						if($class{0} != '\\'){
							$class = '\\Web\\Page\\Controller\\'.$class;
						}
						return new $class($data);
					}
				}
				return $class;
			}
		}
	}
}