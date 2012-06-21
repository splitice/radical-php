<?php
namespace Basic\Structs;

class UserPass implements ILoginDeatils {
	protected $username;
	protected $password;
	
	function __construct($username,$password){
		$this->username = $username;
		$this->password = $password;
	}
	
	/**
	 * @return the $username and $password
	 */
	public function getDetails() {
		return array($this->username, $this->password);
	}
}