<?php
namespace Web\Form\Security;

use Web\Session;
use Basic\Arr\Object\CollectionObject;

class KeyStorage extends CollectionObject {
	function Add(Key $key){
		$ret = parent::Add($key->getId(),$key);
		Session::$data['form_security'] = $this;
		return $ret;
	}
	
	static function AddKey(Key $key){
		if(!isset(Session::$data['form_security'])){
			Session::$data['form_security'] = new static();
		}
		Session::$data['form_security']->Add($key);
	}
	
	/**
	 * @param string $key the id of the key to get
	 * @return HTML\Form\Security\Key
	 */
	static function getKey($key){
		if(!isset(Session::$data['form_security'])){
			throw new \Exception('No security keys in session');
		}
		return Session::$data['form_security'][$key];
	}
}