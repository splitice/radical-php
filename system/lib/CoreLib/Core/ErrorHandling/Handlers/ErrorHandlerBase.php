<?php
namespace Core\ErrorHandling\Handlers;

use Core\ErrorHandling\Errors\Internal\ErrorBase;
use Core\ErrorHandling\Errors\Internal\ErrorException;
use Core\ErrorHandling\Handler;

abstract class ErrorHandlerBase extends Handler {
	
	function __construct(){
		//Itterate
		foreach(\Core\Libraries::getNSExpression('\\ErrorHandling\\Errors\\*') as $class){
			$class::Init();
		}
		
		parent::__construct();
	}
	
	abstract function Error(ErrorBase $error);
	abstract function Exception(ErrorException $error);
}