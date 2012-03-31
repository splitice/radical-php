<?php
namespace Web\Pages\Special;
use Web\PageHandler;

class FileNotFound extends PageHandler\HTMLPageBase {
	function Title(){
		return parent::Title('404 - File Not Found');
	}
	
	function GET() {
		$headers = \Web\PageHandler::$stack->top()->headers;
		$headers->Status(404);

		return new \Web\Template('error', array('error'=>$this));
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