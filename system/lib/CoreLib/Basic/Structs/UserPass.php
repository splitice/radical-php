<?php
namespace Basic\Structs;

class UserPass implements ILoginDetails {
	/**
	 * @var string
	 */
	protected $username;
	
	/**
	 * @var string
	 */
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

	/**
	 * @return array the $username and $password
	 */
	public function getDetails($detail = null) {
		switch ($detail) {
			case 'username':
				return $this->username;
			case 'password':
				return $this->password;
			case null:
				return array('username' => $this->username, 'password' => $this->password);
			default:
				throw new \Exception('Invalid Detail');
		}
	}
	
	/**
	 * Do we have details?
	 * 
	 * @return bool true if we have details
	 */
	function hasDetails(){
		return !empty($this->username) && !empty($this->password);
	}
	
	function __toString(){
		return $this->username.':'.$this->password;
	}
	
	function toArray(){
		return $this->getDetails();
	}
}