<?php
namespace Web\Session\Authentication\Source;

use Database\DynamicTypes\Password;

use Web\Session\ModuleBase;
use Database\Model\TableReferenceInstance;

class NullSource extends ModuleBase implements ISessionSource {
	function Login($username,$password){
		
	}
	function isLoggedIn(){
		return isset(\Web\Session::$data['user']);
	}
	function Logout(){
		unset(\Web\Session::$data['user']);
	}
}