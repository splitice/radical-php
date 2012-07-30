<?php
namespace Core\ErrorHandling\Handlers;

use Core\ErrorHandling\Errors\Internal\ErrorBase;
use Core\ErrorHandling\Errors\Internal\ErrorException;
use Core\ErrorHandling\Handler;

abstract class ErrorHandlerBase extends Handler {
	/**
	 * The namespaces for all error trackers.
	 * 
	 * @radical ns-expr
	 * @var string
	 */
	const ERRORS_EXPR = '\\Core\\ErrorHandling\\Errors\\*';
	
	/**
	 * Calls the init functions for all the error modules
	 */
	function __construct(){
		//Itterate all error trackers
		foreach(\Core\Libraries::get(self::ERRORS_EXPR) as $class){
			$class::Init();
		}
		
		parent::__construct();
	}
	
	abstract function Error(ErrorBase $error);
	abstract function Exception(ErrorException $error);
}