<?php
namespace Basic\Structs;

class UserPass extends LoginDeatils {
	protected $username;
	protected $password;
	
	function __construct($username,$password){
		$this->username = $username;
		$this->password = $password;
	}
	
	/**
	 * @return the $username
	 */
	public function getUsername() {
		return $this->username;
	}
	
	/**
	 * @return the $password
	 */
	public function getPassword() {
		return $this->password;
	}
}