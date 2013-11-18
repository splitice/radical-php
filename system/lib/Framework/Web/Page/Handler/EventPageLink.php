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
	
	function __construct($object, $method, $data = null){
		$this->object = $object;
		$this->method = $method;
		$this->data = $data;
	}
	
	function __toString(){
		return $this->data();
	}
	
	function data($query_params = array()){
		//Build security field
		$securityField = KeyStorage::newKey(array($this,'Execute'));

		$g = $_GET;
		if(isset($g['error'])){
			unset($g['error']);
		}		
		
		//Event details
		$qs = array_merge($g, $query_params);
		$qs[self::EVENT_HANDLER] = $securityField->Store(serialize($this->object));
		$qs[self::EVENT_METHOD] = base64_encode($securityField->Encrypt($this->method));
		$qs[Key::FIELD_NAME] = $securityField->getId();
		
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