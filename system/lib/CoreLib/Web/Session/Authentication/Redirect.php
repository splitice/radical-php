<?php
namespace Web\Session\Authentication;

class Redirect extends Post {
	protected $redirectUrl;
	function __construct($redirectUrl){
		$this->redirectUrl = $redirectUrl;
		parent::__construct(false);
	}
	function Authenticate(){
		if(\Net\Url::fromRequest() == $this->redirectUrl){
			return parent::Authenticate();
		}
		
		//Redirect
		$page = new \Web\Pages\Special\Redirect($this->redirectUrl);
		$page->Execute('GET');
		
		//Bye
		die('Redirecting');
	}
}