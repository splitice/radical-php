<?php
namespace Utility\Net;

class Port {
	protected $port;
	function __construct($port){
		$this->port = (int)$port;
	}
	
	function hasProtocol($protocol = 'tcp'){
		return (bool)$this->getProtocol($protocol);
	}
	
	function getProtocol($protocol = 'tcp'){
		$protocol = strtolower($protocol);
		return getservbyport($this->port,$protocol);
	}
	
	function __toString(){
		return (string)$this->port;
	}
}