<?php
namespace Web\Form\Security;

use Web\Form\Element\HiddenInput;

use Basic\Cryptography\Blowfish;

class Key {
	const FIELD_NAME = '__rp_security_code';
	
	private $key;
	private $id;
	private $storage = array();
	public $expires = -1;
	
	function __construct($callback = null,$ttl = -1){
		//Maximum entropy, minimum data
		$this->id = \Basic\String\Random::GenerateBase64(6).dechex(crc32(session_id().time()));
		$len = strlen($this->id) - rand(0,4);
		$this->id = substr($this->id,0,$len);
		
		$this->key = \Basic\String\Random::GenerateBytes(32);
		$this->callback = $callback;
		if($ttl > 0) $this->expires = $ttl + time();
		KeyStorage::AddKey($this);
	}
	function getId(){
		return $this->id;
	}
	function store($data){
		$this->storage[] = $data;
		return count($this->storage);
	}
	function take($key){
		return $this->storage[$key-1];
	}
	function encrypt($data){
		return Blowfish::Encode($data, $this->key);
	}
	function decrypt($data){
		return Blowfish::Decode($data, $this->key);
	}
	function getElement(){
		return new HiddenInput(self::FIELD_NAME, $this->id);
	}
	static function fromRequest($post = true){
		if($post){
			if(isset($_POST[self::FIELD_NAME])) return $_POST[self::FIELD_NAME];
		}else{
			if(isset($_GET[self::FIELD_NAME])) return $_GET[self::FIELD_NAME];
		}
	}
	static function getData($post = true){
		if($post){
			$data = $_POST;
		}else{
			$data = $_GET;
		}
		unset($data[self::FIELD_NAME]);
		return $data;
	}
	function callback(){
		if($this->callback){
			return call_user_func($this->callback);
		}
	}
}