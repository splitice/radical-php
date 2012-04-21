<?php
namespace Web\Session\Authentication;

use Web\Pages\Special\Redirect;
use Web\Session\Handler\Internal\ISessionHandler;

class Redirect extends Post {
	protected $redirectUrl;
	function __construct($redirectUrl){
		$this->redirectUrl = $redirectUrl;
	}
	function Authenticate(){
		if(\Net\Url::fromRequest() == $this->redirectUrl){
			return parent::Authenticate();
		}
		
		//Redirect
		$page = new Redirect($this->redirectUrl);
		$page->GET();
		
		//Bye
		exit;
	}
}