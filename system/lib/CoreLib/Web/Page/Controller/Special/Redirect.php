<?php
namespace Web\Page\Controller\Special;
use Web\Page\Handler;

class Redirect extends Page\Handler\PageBase {
	protected $url;
	
	function __construct($url){
		$this->url = $url;
	}
	function GET(){
		$headers = \Web\Page\Handler::$stack->top()->headers;
		$headers->Status(301);
		$headers->Add('Location',$this->url);
	}
	function POST(){
		return $this->GET();
	}
}