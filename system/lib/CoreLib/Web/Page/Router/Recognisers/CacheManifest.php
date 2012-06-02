<?php
namespace Web\Page\Router\Recognisers;
use \Web\PageRecogniser\IPageRecognise;
use \Web\Pages;
use Web\Page\Handler;

class CacheManifest implements IPageRecognise {
	static function Recognise(\Net\URL $url){
		$url = $url->getPath();
		if($url->firstPathElement() == 'cache.manifest'){
			$url->removeFirstPathElement();
			
			return PageHandler::Objectify ( 'CacheManifest', $data );
		}
	}
}