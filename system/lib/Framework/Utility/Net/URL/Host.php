<?php
namespace Utility\Net\URL;

class Host {
	function __construct($host){
		$this->host = $host;
	}
	
	function getSplit(){
		return explode('.',$this->host);
	}
	
	function setSplit(array $split){
		$this->host = implode('.',$split);
	}
	
	function getLeaseSignificant(){
		$s = $this->getSplit();
		$r = array_shift($s);
		$this->setSplit($s);
		return $r;
	}
	
	function __toString(){
		return $this->host;
	}
}