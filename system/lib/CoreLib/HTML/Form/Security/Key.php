<?php
namespace HTML\Form\Security;

use HTML\Form\Element\HiddenInput;

use Basic\Cryptography\Blowfish;

class Key {
	private $key;
	private $id;
	private $storage = array();
	public $expires = -1;
	
	function __construct($ttl = -1){
		KeyStorage::AddKey($this);
		$this->id = \Basic\String\Random::GenerateBase64(8);
		$this->key = \Basic\String\Random::GenerateBytes(32);
		if($ttl > 0) $this->expires = $ttl + time();
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
		return new HiddenInput('__rp_security_code', $this->id);
	}
}