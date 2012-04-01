<?php
namespace Web\Session\Handler;

use Database\Model\TableReferenceInstance;

class UserSession extends Internal {
	const FIELD_USERNAME = '*username';
	const FIELD_PASSWORD = '*password';
	private $table;
	
	function __construct(TableReferenceInstance $table){
		$this->table = $table;
		parent::__construct();
	}
	protected function getFields($username,$password){
		return array(static::FIELD_USERNAME=>$username);
	}
	
	function Login($username,$password){
		$class = $this->table->getClass();

		$data = $this->getFields($username,$password);

		$res = $class::fromFields($data);

		if($res){
			$password = $res->getSQLField(static::FIELD_PASSWORD);
			if($password){
				if($password->Compare($password)){
					if($res){
						$this['user'] = $res;
						return true;
					}
				}
			}
		}
		return false;
	}
	function isLoggedIn(){
		return isset($this['user']);
	}
	function Logout(){
		unset($this['user']);
	}
}