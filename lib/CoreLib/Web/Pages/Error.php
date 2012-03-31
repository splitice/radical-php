<?php
namespace Web\Pages;
use Web\Template;

use Web\PageHandler;
use ErrorHandling\Errors\Internal\ErrorException;

class Error extends PageHandler\HTMLPageBase {
	private $error;
	
	function __construct(ErrorException $error){
		$this->error = $error;
	}
	
	
	function GET(){
		return new Template('error',array('error'=>$this->error));
	}
	function POST(){
		return $this->GET();
	}
	
	static function fromURL(\Net\URL $url){
		$page = \Web\PageRecogniser\Recognise::fromURL($url);
		return new static($page);
	}
}