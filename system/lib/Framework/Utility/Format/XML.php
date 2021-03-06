<?php
namespace Utility\Format;
class XML {
	static function repair($str){
		// Specify configuration
		$config = array(
		           'indent'     => true,
		           'input-xml'  => true,
		           'output-xml' => true,
		           'wrap'       => false);
		// Tidy
		$tidy = new \tidy;
		$tidy->parseString($str,$config);
		$tidy->cleanRepair();
		
		return (string)$tidy;
	}
}