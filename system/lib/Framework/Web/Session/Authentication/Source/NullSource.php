<?php
namespace Web\Session\Authentication\Source;

use Model\Database\DynamicTypes\Password;

use Web\Session\ModuleBase;
use Model\Database\Model\TableReferenceInstance;

class NullSource extends ModuleBase implements ISessionSource {
	function login($username,$password){
		
	}
	function isLoggedIn(){
		return isset(\Web\Session::$data['user']);
	}
	function logout(){
		unset(\Web\Session::$data['user']);
	}
}