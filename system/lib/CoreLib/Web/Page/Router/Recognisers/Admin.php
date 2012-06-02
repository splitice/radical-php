<?php
namespace Web\Page\Router\Recognisers;
use Web\Page\Router\IPageRecognise;
use Web\Page\Controller;
use Web\Page\Handler;

class Admin implements IPageRecognise {
	static function Recognise(\Net\URL $url){
		$url = $url->getPath();
		if($url->firstPathElement() == 'admin'){
			$url->removeFirstPathElement();
			$data = array();
			
			$module = $url->firstPathElement();
			if($module){
				return new \Web\Page\Controller\Admin($url,$module);
			}
			return new \Web\Page\Controller\Admin($url);
		}
	}
}