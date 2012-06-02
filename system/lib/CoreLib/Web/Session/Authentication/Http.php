<?php
namespace Web\Session\Authentication;

use Web\Session\ModuleBase;
use Web\Session\Authentication\Source\ISessionSource;

class Http extends ModuleBase implements IAuthenticator {
	function Authenticate(){
		$headers = \Web\Page\Handler::$stack->top()->headers;
		$headers->Status(401);
		$headers->Add('WWW-Authenticate','Basic realm="Site Login"');
		$headers->Output();
		echo 'Text to send if user hits Cancel button';
		exit;
	}
	function AuthenticationError($error = 'Username or Password Invalid'){
		die('Login Failed: '.$error);
		//@todo complete
	}
	function Init(ISessionSource $handler){
		if(isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_PW']){
			$username = $_SERVER['PHP_AUTH_USER'];
			$password = $_SERVER['PHP_AUTH_PW'];
			
			$success = $handler->Login($username,$password);
			
			if(!$success){
				return $this->AuthenticationError();
			}
		}
	}
}