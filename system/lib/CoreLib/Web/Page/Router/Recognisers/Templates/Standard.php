<?php
namespace Web\Page\Router\Recognisers\Templates;
use Basic\String\Format;
use Web\PageRecogniser\IPageRecognise;
use Web\Pages;
use Web\Page\Handler;

class Standard implements IPageRecognise {
	static $match = array();
	static function Recognise(\Net\URL $url){
		$path = $url->getPath()->getPath(true);
		foreach(static::$match as $expr=>$class){
			$match = Format::Consume($path, $expr);
			if($match){
				return new $class($match);
			}
		}
	}
}