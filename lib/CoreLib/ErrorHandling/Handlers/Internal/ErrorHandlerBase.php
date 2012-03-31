<?php
namespace ErrorHandling\Handlers\Internal;
use \ErrorHandling\Errors\Internal\ErrorBase;
use ErrorHandling\Errors\Internal\ErrorException;

abstract class ErrorHandlerBase extends \ErrorHandling\Handler {
	
	function __construct(){
		//Itterate
		foreach(\ClassLoader::getNSExpression('\\ErrorHandling\\Errors\\*') as $class){
			$class::Init();
		}
		
		parent::__construct();
	}
	
	abstract function Error(ErrorBase $error);
	abstract function Exception(ErrorException $error);
}