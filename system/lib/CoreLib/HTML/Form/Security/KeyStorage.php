<?php
namespace HTML\Form\Security;

use Web\Session;
use Basic\ArrayLib\Object\CollectionObject;

class KeyStorage extends CollectionObject {
	function Add(Key $key){
		return parent::Add($key->getId(),$key);
	}
	
	static function AddKey(Key $key){
		if(!isset(Session::$data['form_security'])){
			Session::$data['form_security'] = new static();
		}
		Session::$data['form_security']->Add($key);
	}
	
	/**
	 * @return HTML\Form\Security\Key
	 */
	static function GetKey(){
		if(!isset(Session::$data['form_security'])){
			throw new \Exception('No security keys in session');
		}
		return Session::$data['form_security'][$key];
	}
}