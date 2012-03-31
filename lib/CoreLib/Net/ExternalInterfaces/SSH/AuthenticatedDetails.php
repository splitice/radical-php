<?php
namespace Net\ExternalInterfaces\SSH;

class AuthenticatedDetails {
	private $method;
	private $arguments;
	
	function __construct($method,array $arguments = array()){
		$this->method = $method;
		$this->arguments = $arguments;
	}
	
	function Execute(Authenticate $object){
		call_user_func_array(array($object,$this->method), $this->arguments);
	}
}