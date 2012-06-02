<?php
namespace Web\Page\Controller\Special;

use Web\Page\Handler\HTMLPageBase;
use Web\Page\Handler;

class FileNotFound extends HTMLPageBase {
	function Title(){
		return parent::Title('404 - File Not Found');
	}
	
	function GET() {
		$headers = \Web\Page\Handler::$stack->top()->headers;
		$headers->Status(404);

		return new \Web\Template('error', array('error'=>$this),'framework');
	}
	function getHeading(){
		return $this->Title();
	}
	function getMessage(){
		return 'You requested a URL that we could not find. Please check the spelling of the URL and try again.';
	}
	function POST() {
		return $this->GET ();
	}
}