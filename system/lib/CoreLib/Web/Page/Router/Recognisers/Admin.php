<?php
namespace Web\Page\Router\Recognisers;
use \Web\PageRecogniser\IPageRecognise;
use \Web\Pages;
use Web\Page\Handler;

class Admin implements IPageRecognise {
	static function Recognise(\Net\URL $url){
		$url = $url->getPath();
		if($url->firstPathElement() == 'admin'){
			$url->removeFirstPathElement();
			$data = array();
			
			$module = $url->firstPathElement();
			if($module){
				return new \Web\Pages\Admin($url,$module);
			}
			return new \Web\Pages\Admin($url);
		}
	}
}