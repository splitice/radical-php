<?php
namespace Web\Page\Router\Recognisers;

use Web\Page\Router\IPageRecognise;
use Web\Page\Controller;
use Web\Page\Handler;

class CacheManifest implements IPageRecognise {
	static function Recognise(\Net\URL $url){
		$url = $url->getPath();
		if($url->firstPathElement() == 'cache.manifest'){
			$url->removeFirstPathElement();
			
			return Page\Handler::Objectify ( 'CacheManifest', $data );
		}
	}
}