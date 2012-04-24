<?php
namespace Web\PageHandler;

use HTML\Form\Security\KeyStorage;

use HTML\Form\Security\Key;

abstract class EventPageBase extends HTMLPageBase {
	protected $eventKey;
	function Execute($method = 'GET'){
		//Check for an event
		if($method == 'POST'){
			$id = Key::fromRequest();
			if($id){
				$key = KeyStorage::GetKey($id);
				if($key){
					$this->eventKey = $key;
					$result = $key->Callback();
					if($result){
						return $result;
					}
				}else{
					throw new \Exception('Form submission invalid');
				}
			}
		}
		
		return parent::Execute($method);
	}
}