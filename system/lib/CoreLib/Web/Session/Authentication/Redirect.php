<?php
namespace Web\Session\Authentication;

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
		$page = new \Web\Pages\Special\Redirect($this->redirectUrl);
		$page->GET();
		
		//Bye
		exit;
	}
}