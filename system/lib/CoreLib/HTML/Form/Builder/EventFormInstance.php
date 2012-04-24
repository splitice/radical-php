<?php
namespace HTML\Form\Builder;
use HTML\Form\Security\Key;

use HTML\Form\Builder\FormInstance;

class EventFormInstance extends FormInstance {
	private $eventHandler;
	private $eventMethod;
	
	function __construct($handler,$method='execute'){
		parent::__construct();
		
		//store even description
		$this->eventHandler = $handler;
		$this->eventMethod = $method;
		
		//Build security field
		$securityField = new Key();
		
		//Event details
		$this->hidden('__rp_eventA',$securityField->Store(serialize($handler)));
		$this->hidden('__rp_eventB',base64_encode($securityField->Encrypt($this->eventMethod)));
		
		//Security event
		$this->Add($securityField->getElement());
	}
	
	function Execute($data){
		return call_user_func(array($this->eventHandler,$this->eventMethod), $data);
	}
}