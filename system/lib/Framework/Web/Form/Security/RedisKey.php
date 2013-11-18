<?php
namespace Web\Form\Security;

class RedisKey extends Key {
	public $session_id;
	
	function __construct($callback = null,$ttl = -1){
		$this->session_id = session_id();
		parent::__construct($callback, $ttl);
	}
}