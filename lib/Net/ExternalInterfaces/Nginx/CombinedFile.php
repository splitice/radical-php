<?php
namespace Net\ExternalInterfaces\Nginx;
class CombinedFile {
	private $out = '';
	
	function __construct() {
	
	}
	
	function Add($str) {
		$this->out .= $str . "\r\n";
	}
	
	function Out() {
		return $this->out;
	}
}
?>