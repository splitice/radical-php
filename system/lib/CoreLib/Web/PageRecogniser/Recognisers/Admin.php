<?php
namespace Web\PageRecogniser\Recognisers;
use \Web\PageRecogniser\IPageRecognise;
use \Web\Pages;
use Web\PageHandler;

class Admin implements IPageRecognise {
	static function Recognise(\Net\URL $url){
		$url = $url->getPath();
		if($url->firstPathElement() == 'admin'){
			$url->removeFirstPathElement();
			$data = array();
			
			$module = $url->firstPathElement();
			if($module){
				return new \Web\Pages\Admin($module,$url);
			}
		}
	}
}