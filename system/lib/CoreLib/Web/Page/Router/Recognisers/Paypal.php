<?php
namespace Web\Page\Router\Recognisers;

use Web\Page\Router\IPageRecognise;
use Web\Pages;
use Web\Page\Handler;

class Paypal implements IPageRecognise {
	static function Recognise(\Net\URL $url){
		$url = $url->getPath();
		if($url->firstPathElement() == 'paypal.html'){
			$url->removeFirstPathElement();
			return Page\Handler::Objectify ( 'Paypal', array('action'=>$_GET['action']) );
		}
	}
}