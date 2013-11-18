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
			throw new \Exception("This should never happen if Redis is in use");
			/*$redis = new \Redis\Store($this->redis_key($key));
			$redis->set($data);
			$data = $redis;*/
		}
		$ret = parent::Add($key->getId(),$data);
		Session::$data['form_security'] = $this;
		return $ret;
	}
	
	static function AddKey(Key $key){
		if(self::USE_REDIS){
			RedisStorage::set($key->getId(), $key);
		}else{
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
	}
	
	/**
	 * @param string $key the id of the key to get
	 * @return HTML\Form\Security\Key
	 */
	static function getKey($key){
		if(self::USE_REDIS){
			$key = RedisStorage::get($key);
			if($key instanceof RedisKey){
				if($key->session_id != session_id()){
					throw new \Exception("Security Exception, session id does not match");
				}
				
				return $key;
			}else{
				return;
			}
		}else{
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
	
	static function newKey($data){
		if(self::USE_REDIS){
			return new RedisKey($data);
		}else{
			return new Key($data);
		}
	}
}