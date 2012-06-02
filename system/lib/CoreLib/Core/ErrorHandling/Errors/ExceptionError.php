<?php
namespace ErrorHandling\Errors;

class ExceptionError extends Internal\ErrorBase {
	const HEADER = 'Site Error (%s)';
	private $ex;
	
	function __construct(\Exception $ex,$fatal = true){
		$this->ex = $ex;
		
		//Build Error page
		if(\Server::isProduction()){
			$message = 'An exception has occured in the script.';
			global $_ADMIN_EMAIL;
			if(isset($_ADMIN_EMAIL)){
				$message .= ' Please report this to an administrator at '.$_ADMIN_EMAIL.'.';
			}
		}else{
			$message = 'An exception occured at '.$ex->getFile().'@'.$ex->getLine().': '.$ex->getMessage();
		}
		
		$header = sprintf(static::HEADER,ltrim(get_class($ex),'\\'));
		parent::__construct($message,$header,$fatal);
	}
	
	function getTraceOutput(){
		return $this->ex->getTraceAsString();
	}
}