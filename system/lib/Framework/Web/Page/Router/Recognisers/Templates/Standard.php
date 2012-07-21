<?php
namespace Web\Page\Router\Recognisers\Templates;

use Utility\Net\URL;
use Basic\String\Format;
use Web\Page\Router\IPageRecognise;
use Web\Page\Controller;
use Web\Page\Handler;

class Standard implements IPageRecognise {
	static $match = array();
	static function Recognise(URL $url){
		$path = $url->getPath()->getPath(true);
		foreach(static::$match as $expr=>$class){
			$match = Format::Consume($path, $expr);
			if($match){
				if($class{0} != '\\'){
					$class = '\\Web\\Page\\Controller\\'.$class;
				}
				return new $class($match);
			}
		}
	}
}