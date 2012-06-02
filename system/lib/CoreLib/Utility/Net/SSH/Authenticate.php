<?php
namespace Net\ExternalInterfaces\SSH;

class Authenticate {
	private $ssh;
	/**
	 * @var AuthenticatedDetails
	 */
	private $auth;
	
	function __construct(&$ssh){
		$this->ssh = $ssh;
	}
	
	function Password($username,$password){
		$this->auth = new AuthenticatedDetails(__FUNCTION__,array($username,$password));
		return ssh2_auth_password($this->ssh,$username,$password);
	}
	
	function Authenticate(Authenticate $auth){
		$auth->Execute($this);
	}
	
	function Execute(Authenticate $object){
		$this->auth->Execute($object);
	}
}