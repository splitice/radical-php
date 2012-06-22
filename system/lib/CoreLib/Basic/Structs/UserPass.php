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
	public function getDetails($detail = null) {
		switch ($detail) {
			case 'username':
				return $this->username;
			case 'password':
				return $this->password;
			default:
				return array('username => $this->username, 'password' => $this->password);
		}
	}
}