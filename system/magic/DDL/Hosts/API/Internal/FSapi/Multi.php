<?php
namespace DDL\Hosts\API\Internal\FSapi;
use DDL\Hosts\API\Internal\FSapi;

class Multi {
	private $callback;
	private $mh;
	private $F;
	
	function __construct($callback,$mh,$F){
		$this->callback = $callback;
		$this->mh = $mh;
		$this->F = $F;
	}
	function isRecaptcha($data){
		if(stripos($data,'Recaptcha.create')){
			return true;
		}
		return false;
	}
	function completeReC($data){
		$page = $data->getPage()->getContent();
		
		if(!preg_match('#Recaptcha\.create("([^"]+)"#', $page, $m)){
			return;
		}
		$jskey = $m[1];
		$url = 'http://api-secure.recaptcha.net/challenge?k='.$jskey;
		
		$http = new \HTTP\Fetch($url);
		$http->setReferer($data->getPage()->getUrl());
		$script = $http->Execute();

		$post = array('recaptcha_challenge_field'=>'','recaptcha_response_field'=>'');
	}
	function Callback($data){
		$page = $data->getPage()->getContent();
		$page = @json_decode($page);
		if(!$page){//Error
			if($this->isRecaptcha($page)){
				if($this->completeReC($data)){				
					$this->mh->Add($this->F,array($this,'Callback'));//Re-add
				}
			}
		}else{
			$status = FSapi::_LinkCheck($page);
			$status = new MultiContentContainer($status);
			call_user_func($this->callback,$status);
		}
	}
}