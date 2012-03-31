<?php
namespace Net;

use Basic\Cryptography\CRC32;

class IP {
	private $ip;
	
	function __construct($ip){
		$this->ip = $ip;
	}
	
	/**
	 * @return the $ip
	 */
	public function getIp() {
		return $this->ip;
	}
	
	private $_version;
	public function getVersion(){
		if($this->_version) return $this->_version;
		if(filter_var ( $this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 )){
			$this->_version = 6;
		}elseif(filter_var ( $this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )){
			$this->_version = 4;
		}
		return $this->_version;
	}
	
	function isValid(){
		return filter_var ( $this->ip, FILTER_VALIDATE_IP );
	}
	
	function Hash(){
		return CRC32::Hash($this->ip);
	}

	function __toString(){
		return $this->ip;
	}
}