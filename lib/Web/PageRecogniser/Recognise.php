<?php
namespace Web\PageRecogniser;

class Recognise extends \Core\Object {
	static $__dependencies = array('interface.Web.PageRecogniser.IPageRecognise','interface.Web.API.IAPIModule');
	
	static function fromRequest(){
		$url = \Net\URL::fromRequest();
		return static::fromURL($url);
	}
	static function fromURL(\Net\URL $url){
		foreach(\ClassLoader::getNSExpression('\\Web\\PageRecogniser\\Recognisers\\*') as $class){
			if(\oneof($class,'\\Web\PageRecogniser\\IPageRecognise')){
				$r = $class::Recognise(clone $url);
				if($r){
					return $r;
				}
			}
		}
	}
}