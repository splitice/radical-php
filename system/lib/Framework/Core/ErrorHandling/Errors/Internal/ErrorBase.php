<?php
namespace Core\ErrorHandling\Errors\Internal;

use Core\ErrorHandling\Handler;

abstract class ErrorBase extends ErrorException {
	function __construct($message,$header = 'An error has occured',$fatal=false){
		parent::__construct($message,$header,$fatal);
		
		$errorHandler = Handler::getInstance();
		$errorHandler->Error($this);
	}
	static function init(){
		
	}
}