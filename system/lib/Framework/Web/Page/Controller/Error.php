<?php
namespace Web\Page\Controller;

use Web\Page\Router\Recognise;
use Web\Page\Handler\HTMLPageBase;
use Web\Template;
use Web\Page\Handler;
use Core\ErrorHandling\Errors\Internal\ErrorException;

class Error extends HTMLPageBase {
	private $error;
	
	function __construct(ErrorException $error){
		$this->error = $error;
	}

	/**
	 * Handle GET request
	 *
	 * @throws \Exception
	 */
	function gET(){
		return new Template('error',array('error'=>$this->error),'framework');
	}
	
	/**
	 * Handle POST request
	 *
	 * @throws \Exception
	 */
	function pOST(){
		return $this->GET();
	}
	
	static function fromURL(\Utility\Net\URL $url){
		$page = Recognise::fromURL($url);
		return new static($page);
	}
}