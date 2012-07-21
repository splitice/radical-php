<?php
namespace Web\Page\API\Module;

class Session extends APIBase {
	function getUserDetails(){
		$output=array();
		if(\Web\Session::$auth->isLoggedIn()){
			$user = \Web\Session::$auth->getUser();
			$output['id'] = $user->getId();
			$output['name'] = $user->getUsername();
			$output['admin'] = $user->isAdmin();
		}else{
			$output['id'] = null;
			$output['name'] = 'Guest';
			$output['admin'] = false;
		}
		return $output;
	}
	function Login(){
		$success = \Web\Session::$auth->Login($this->data['username'],$this->data['password']);
		return $success;
	}
}