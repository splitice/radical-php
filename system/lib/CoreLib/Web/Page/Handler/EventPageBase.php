<?php
namespace Web\Page\Handler;

use Web\Form\Security\KeyStorage;

use Web\Form\Security\Key;

abstract class EventPageBase extends HTMLPageBase {
	protected $eventKey;
	/** 
	 * Intercept Execute calls and check for POST events
	 * If there is a post event submission do it.
	 * 
	 * @see Web\Page\Handler.PageBase::Execute()
	 */
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
		
		//Normal execution
		return parent::Execute($method);
	}
}