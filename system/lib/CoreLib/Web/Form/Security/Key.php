<?php
namespace HTML\Form\Security;

use HTML\Form\Element\HiddenInput;

use Basic\Cryptography\Blowfish;

class Key {
	const FIELD_NAME = '__rp_security_code';
	
	private $key;
	private $id;
	private $storage = array();
	public $expires = -1;
	
	function __construct($callback = null,$ttl = -1){
		$this->id = \Basic\String\Random::GenerateBase64(8);
		$this->key = \Basic\String\Random::GenerateBytes(32);
		$this->callback = $callback;
		if($ttl > 0) $this->expires = $ttl + time();
		KeyStorage::AddKey($this);
	}
	function getId(){
		return $this->id;
	}
	function Store($data){
		$this->storage[] = $data;
		return count($this->storage);
	}
	function Take($key){
		return $this->storage[$key-1];
	}
	function Encrypt($data){
		return Blowfish::Encode($data, $this->key);
	}
	function Decrypt($data){
		return Blowfish::Decode($data, $this->key);
	}
	function getElement(){
		return new HiddenInput(self::FIELD_NAME, $this->id);
	}
	static function fromRequest(){
		if(isset($_POST[self::FIELD_NAME])) return $_POST[self::FIELD_NAME];
	}
	static function getData(){
		$data = $_POST;
		unset($data[self::FIELD_NAME]);
		return $data;
	}
	function Callback(){
		if($this->callback){
			return call_user_func($this->callback);
		}
	}
}