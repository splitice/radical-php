<?php
namespace CLI\Output\Error;
use ErrorHandling\Errors\Internal;
use ErrorHandling\IToCode;
use CLI\Console\Colors;

class OutputError extends Internal\ErrorBase implements IToCode {
	protected $code;
	
	function __construct($message,$code){
		//Store Code
		$this->code = strtoupper($code);

		//Do parent
		parent::__construct($message,'',false);
		
		//Send to Exception
		$errorHandler = \ErrorHandling\Handler::getInstance();
		$errorHandler->Exception($this);
	}
	
	function getColor(){
		switch($this->code){
			case 'FATAL':
				return 'red';
				break;
			case 'ERROR':
				return 'light_red';
				break;
			case 'WARN':
				return 'yellow';
				break;
			case 'NOTICE':
				return 'blue';
				break;
			default:
				throw new \Exception('Invalid Error Code type: '.$this->code);
				break;
		}
	}
	
	function toCode(){
		$code = Colors::getInstance()->getColoredString($this->code,$this->getColor());
		return $code;
	}
	function getPage(){
		$this->heading = 'This Error should only be displayed in Command Line!!';
		return parent::getPage();
	}
}