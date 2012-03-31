<?php
namespace Web\Pages\Special;
use Web\PageHandler;

class Redirect extends PageHandler\PageBase {
	protected $url;
	
	function __construct($url){
		$this->url = $url;
	}
	function GET(){
		$headers = \Web\PageHandler::$stack->top()->headers;
		$headers->Status(301);
		$headers->Add('Location',$this->url);
	}
	function POST(){
		return $this->GET();
	}
}