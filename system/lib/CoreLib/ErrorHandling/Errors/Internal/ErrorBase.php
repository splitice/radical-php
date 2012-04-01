<?php
namespace ErrorHandling\Errors\Internal;

abstract class ErrorBase extends ErrorException {
	function __construct($message,$header = 'An error has occured',$fatal=false){
		parent::__construct($message,$header,$fatal);
		
		$errorHandler = \ErrorHandling\Handler::getInstance();
		$errorHandler->Error($this);
	}
	static function Init(){
		
	}
}