<?php
namespace Utility\Net;

class CIDR {
	protected $cidr;
	
	function __construct($cidr){
		$this->cidr = $cidr;
	}
	
	function contains($ip){
		list ($net, $mask) = split ("/", $this->cidr);
		
		$ip_net = ip2long ($net);
		$ip_mask = ~((1 << (32 - $mask)) - 1);
		
		$ip_ip = ip2long ($ip);
		
		$ip_ip_net = $ip_ip & $ip_mask;
		
		return ($ip_ip_net == $ip_net);
	}
	
	function validate(){
		return preg_match('^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(\/(\d|[1-2]\d|3[0-2]))$', $this->cidr);
	}
	
	function __toString(){
		return (string)$this->cidr;
	}
}