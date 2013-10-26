<?php
namespace Web\Form\Security;

use Web\Session;
use Basic\Arr\Object\CollectionObject;

class KeyStorage extends CollectionObject {
	const USE_REDIS = true;
	
	private function redis_key(Key $key){
		return 'ks_'.$key->getId();
	}
	function Add(Key $key){
		$data = $key;
		if(self::USE_REDIS){
			$redis = new \Redis\Store($this->redis_key($key));
			$redis->set($data);
			$data = $redis;
		}
		$ret = parent::Add($key->getId(),$data);
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
		$ret = Session::$data['form_security'][$key];		
		if($ret instanceof \Redis\Store){
			return $ret->get();
		}
		return $ret;
	}
}