<?php
namespace Web\Form\Builder;
use Web\Form\Security\Key;

use Web\Form\Builder\FormInstance;

class EventFormInstance extends FormInstance {
	const EVENT_HANDLER = '__rp_eventA';
	const EVENT_METHOD = '__rp_eventB';
	
	private $eventHandler;
	private $eventMethod;
	
	function __construct($handler,$method='execute'){
		parent::__construct();
		
		//store even description
		$this->eventHandler = $handler;
		$this->eventMethod = $method;
		
		//Build security field
		$securityField = new Key(array($this,'Execute'));
		
		//Event details
		$this->hidden(self::EVENT_HANDLER,$securityField->Store(serialize($handler)));
		$this->hidden(self::EVENT_METHOD,base64_encode($securityField->Encrypt($this->eventMethod)));
		
		//Security event
		$this->Add($securityField->getElement());
		
		//Ensure its post, security fields only work on post requests currently.
		$this->method('post');
	}
	
	function execute($data = null){
		//data from post
		if($data === null){
			$data = Key::getData();
		}
		
		//Clean up event data
		unset($data[self::EVENT_HANDLER],$data[self::EVENT_METHOD]);
		
		//execute event
		return call_user_func(array($this->eventHandler,$this->eventMethod), $data);
	}
}