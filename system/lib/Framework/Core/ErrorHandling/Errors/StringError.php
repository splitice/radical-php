<?php
namespace Core\ErrorHandling\Errors;

class StringError extends Internal\ErrorBase {
	const HEADER = 'Site Error (PHP)';
	
	private $_type;
	private $_file;
	private $_line;
	private $_backtrace;
	
	function __construct($message, $type, $file, $line, $backtrace){
		$this->_type = $type;
		$this->_file = $file;
		$this->_line = $line;
		$this->_backtrace = $backtrace;
		
		parent::__construct($message);
	}
	
	function getTraceOutput(){
		return $this->_backtrace;
	}
}