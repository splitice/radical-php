<?php
namespace ErrorHandling;
use \ErrorHandling\Errors\Internal\ErrorException;

/* STATIC */
abstract class Handler {
	static $instance;
	
	function __construct(){
		self::$instance = $this;
	}
	
	static function getInstance(){
		if(!self::$instance){
			self::$instance = new Handlers\NullErrorHandler();
		}
		return self::$instance;
	}
	
	static private function handleException(ErrorException $ex, $fatal = false){
		if($ex->isFatal() || $fatal){
			Handler::getInstance()->Exception($ex);
		}
	}
	static function Handle($callback,$argument = array()){
		try {
			call_user_func_array($callback, $argument);
		}
		catch(ErrorException $ex){
			self::handleException($ex,true);
		}
		catch(\Exception $ex){
			$ex = new \ErrorHandling\Errors\ExceptionError($ex,false);
			self::handleException($ex,true);
		}
	}
	
	function isNull(){
		return false;
	}
}