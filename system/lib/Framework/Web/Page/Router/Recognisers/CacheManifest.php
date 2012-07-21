<?php
namespace Web\Page\Router\Recognisers;

use Utility\Net\URL;
use Web\Page\Router\IPageRecognise;
use Web\Page\Controller;
use Web\Page\Handler;

class CacheManifest implements IPageRecognise {
	static function Recognise(URL $url){
		$url = $url->getPath();
		if($url->firstPathElement() == 'cache.manifest'){
			$url->removeFirstPathElement();
			
			return Handler::Objectify ( 'CacheManifest', $data );
		}
	}
}