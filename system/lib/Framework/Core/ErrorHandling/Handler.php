<?php
namespace Core\ErrorHandling;

use Core\ErrorHandling\Errors\Internal\ErrorException;

/* STATIC */
abstract class Handler {
	static $instance;
	
	/**
	 * Create instance and store in singleton
	 */
	function __construct(){
		self::$instance = $this;
	}
	
	/**
	 * Get instance (singleton type pattern)
	 * 
	 * @return \Core\ErrorHandling\Handler instance of
	 */
	static function getInstance(){
		if(!self::$instance){
			self::$instance = new Handlers\OutputErrorHandler();
		}
		return self::$instance;
	}
	
	/**
	 * Is called when an unhandled exception is received.
	 * 
	 * @param ErrorException $ex the error exception defining the unhandled exception
	 * @param bool $fatal usually is true (fatal)
	 */
	static private function handleException(ErrorException $ex, $fatal = false){
		if($ex->isFatal() || $fatal){
			Handler::getInstance()->Exception($ex);
		}
	}
	
	/**
	 * Execute $callback from within the scope of the error handling system.
	 * Any errors will be handled by the correct error handler.
	 * 
	 * @param callback $callback to execute
	 * @param array $arguments to pass to $callback
	 * @returns mixed the result of $callback(...)
	 */
	static function Handle($callback,$arguments = array()){
		try {
			return call_user_func_array($callback, $arguments);
		}
		catch(ErrorException $ex){
			self::handleException($ex,true);
		}
		catch(\Exception $ex){
			$ex = new Errors\ExceptionError($ex,false);
			self::handleException($ex,true);
		}
	}
	
	function isNull(){
		return false;
	}
}