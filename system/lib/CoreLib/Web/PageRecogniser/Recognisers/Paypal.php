<?php
namespace Web\PageRecogniser\Recognisers;
use \Web\PageRecogniser\IPageRecognise;
use \Web\Pages;
use \Web\PageHandler;

class Paypal implements IPageRecognise {
	static function Recognise(\Net\URL $url){
		$url = $url->getPath();
		if($url->firstPathElement() == 'paypal.html'){
			$url->removeFirstPathElement();
			return PageHandler::Objectify ( 'Paypal', array('action'=>$_GET['action']) );
		}
	}
}