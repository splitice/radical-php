<?php
namespace Web\Page\Handler;

use Web\Form\Security\KeyStorage;
use Web\Form\Security\Key;
use Web\Page\Controller\Special\Redirect;
use Utility\Net\URL;

trait TEventPageBase {
	protected $eventKey;
	
	protected function _processEvent($post = true){
		$id = Key::fromRequest($post);
		if(!empty($id)){
			$key = KeyStorage::GetKey($id);
			if($key){
				$this->eventKey = $key;
				$result = $key->Callback();
				if($result){
					return $result;
				}
			}else{
				throw new \Exception('Event invalid (session timeout?)');
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
		} else if ($method == 'GET'){
			$r = $this->_processEvent(false);
			if($r) {
				$request = new PageRequest($r);
				return $request->execute($method);
			}
		}
	
		//Normal execution
		return parent::Execute($method);
	}
	
	protected function event_redirect(){
		if($_SERVER['REQUEST_METHOD'] == 'POST')
			return new Redirect((string)URL::fromRequest());
		
		$url = URL::fromRequest();
		$qs = $url->getQuery();
		foreach($qs as $k=>$v){
			if(substr($k, 0, 2) == '__'){
				unset($qs[$k]);
			}
		}
		$url->setQuery($qs);

		return new Redirect((string)$url);
	}
}