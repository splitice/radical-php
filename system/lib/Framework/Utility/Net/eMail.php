<?php
namespace Utility\Net;

class eMail {
	protected $local;
	protected $domain;
	
	function __construct($local,$domain){
		$this->local = $local;
		$this->domain = $domain;
	}
	
	/**
	 * @return the $local
	 */
	public function getLocal() {
		return $this->local;
	}

	/**
	 * @return the $domain
	 */
	public function getDomain() {
		return $this->domain;
	}
	
	function validateDNS(){
		return checkdnsrr($this->domain,'MX');
	}

	static function fromAddress($address){
		$parts = explode('@',$address);
		if(count($parts) != 2){
			return;
		}
		if(!preg_match('#(?:[a-zA-Z0-9\.\-]+)#', $parts[1])){
			return;
		}
		if(!strpos($parts[1], '.'))
			return;//cant start with a . and must have atleast one
		
		return new static($parts[0],$parts[1]);
	}
}