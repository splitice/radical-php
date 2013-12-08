<?php
namespace Web\Page\Handler;

use Web\Form\Security\Key;
use Web\Form\Security\KeyStorage;
class EventPageLink {
	const EVENT_HANDLER = '__rp_eventA';
	const EVENT_METHOD = '__rp_eventB';

	private $object;
	private $method;
	private $data;
	private $securityField = null;
	private $eHandler;
	private $eMethod;
	
	function __construct($object, $method, $data = null){
		$this->object = $object;
		$this->method = $method;
		$this->data = $data;
	}
	
	function getObject(){
		return $this->object;
	}
	
	function __toString(){
		return $this->data();
	}
	
	function data($query_params = array()){
		//Build security field
		if($this->securityField === null){
			$this->securityField = KeyStorage::newKey(array($this,'Execute'));
			$this->eHandler = $this->securityField->Store(serialize($this->object));
			$this->eMethod = base64_encode($this->securityField->Encrypt($this->method));
		}

		$g = $_GET;
		if(isset($g['error'])){
			unset($g['error']);
		}
		if(isset($g['eid'])){
			unset($g['eid']);
		}	
		
		//Event details
		$qs = array_merge($g, $query_params);
		$qs[self::EVENT_HANDLER] = $this->eHandler;
		$qs[self::EVENT_METHOD] = $this->eMethod;
		$qs[Key::FIELD_NAME] = $this->securityField->getId();
		
		$str_qs = '?'.http_build_query($qs);
		
		return $str_qs;
	}
	
	function link($data = null){
		if($data == null){
			return (string)$this;
		}
		
		return new EventPageLink($this->object, $this->method, $data);
	}
	
	function Execute(){
		return $this->object->{$this->method}($this->data,Key::getData(false));
	}
}