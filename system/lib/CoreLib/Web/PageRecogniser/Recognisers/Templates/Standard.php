<?php
namespace Web\PageRecogniser\Recognisers\Templates;
use Basic\String\Format;
use \Web\PageRecogniser\IPageRecognise;
use \Web\Pages;
use \Web\PageHandler;

class Standard implements IPageRecognise {
	static $match = array(
	//	'/page/%(id)d'=>'\\Web\\Pages\\Page'
	);
	static function Recognise(\Net\URL $url){
		foreach(static::$match as $expr=>$class){
			$match = Format::Consume($url->getPath(), $expr);
			if($match !== false){
				return new $class($match);
			}
		}
	}
}