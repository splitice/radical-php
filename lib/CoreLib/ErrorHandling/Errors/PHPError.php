<?php
namespace ErrorHandling\Errors;

class PHPError extends Internal\ErrorBase {
	const HEADER = 'Site Error (PHP)';
	
	function __construct($errno, $msg_text, Structs\LocationReference $where){
		//Build Error page
		if(!\Server::isProduction() || \Server::isCLI()){
			$message = 'A PHP error occured at '.$where->toShort().': '.$msg_text;
		}else{
			$message = 'An error has occured in the script.';
			global $_ADMIN_EMAIL;
			if(isset($_ADMIN_EMAIL)){
				$message .= ' Please report this to an administrator at '.$_ADMIN_EMAIL.'.';
			}
		}

		$fatal = false;
		if($errno == E_CORE_ERROR || $errno == E_ERROR || $errno == E_RECOVERABLE_ERROR || $errno == E_USER_ERROR){
			$fatal = true;
		}
		
		parent::__construct($message,static::HEADER,$fatal);
		
		//CLI Display PHP errors
		if(!$fatal && \Server::isCLI()){
			switch($errno){
				case E_COMPILE_WARNING:
				case E_CORE_WARNING:
				case E_USER_WARNING:
				case E_WARNING:
					\CLI\Output\Error::Warning($message);
					break;
				case E_NOTICE:
				case E_STRICT:
				case E_USER_NOTICE:
				case E_USER_DEPRECATED:
				case E_DEPRECATED:
					\CLI\Output\Error::Notice($message);
					break;
			}
		}
	}
	static function Init(){
		set_error_handler ( array (get_called_class(), 'Handler' ) );
	}
	static function Handler($errno, $msg_text, $errfile, $errline) {
		//die(var_dump($errno,$msg_text,$errfile,$errline));	
		if (! (error_reporting () & $errno)) {
			return true;
		}
	
		if ($errno != E_STRICT) { //E_STRICT, well we would like it but not PEAR
			new static($errno, $msg_text, new Structs\LocationReference($errfile, $errline));
			return true;
		}
	
		return true;
	}
}