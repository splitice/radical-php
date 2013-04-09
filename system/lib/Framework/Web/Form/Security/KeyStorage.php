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
		$data = Session::$data;
		
		$data->lock_open();
		
		if($data instanceof \Web\Session\Storage\Internal)
			$data->refresh();
		
		if(!isset(Session::$data['form_security'])){
			$data['form_security'] = new static();
		}
		$data['form_security']->Add($key);

		$data->lock_close();
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