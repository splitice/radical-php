<?php
namespace Web\Page\Handler;

use Web\Form\Security\KeyStorage;
use Web\Form\Security\Key;

trait TEventPageBase {
	protected $eventKey;
	
	protected function _processEvent(){
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
	
	/**
	 * Intercept Execute calls and check for POST events
	 * If there is a post event submission do it.
	 *
	 * @see Web\Page\Handler.PageBase::Execute()
	 */
	function execute($method = 'GET'){
		//Check for an event
		if($method == 'POST'){
			$r = $this->_processEvent();
			if($r) {
				$request = new PageRequest($r);
				return $request->execute($method);
			}
		}
	
		//Normal execution
		return parent::Execute($method);
	}
}