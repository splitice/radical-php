<?php
namespace Core\ErrorHandling\Errors\Internal;
use Web\Page\Controller;
use Core\ErrorHandling\IErrorException;

class SerializableErrorException implements IErrorException {
	protected $heading;
	protected $fatal = false;
	protected $trace_output;
	protected $message;
	
	function __construct(ErrorException $ex){
		$this->heading = $ex->getHeading();
		$this->fatal = $ex->isFatal();
		$this->message = $ex->getMessage();
		$this->trace_output = $ex->getTraceAsString();
		
	}

	/**
	 * @return the $header
	 */
	public function getHeading() {
		return $this->heading;
	}
	
	function isFatal(){
		return $this->fatal;
	}
	
	function getMessage(){
		return $this->message;
	}

	function getPage(){
		return new Controller\Error($this);
	}
	
	function getTraceOutput(){
		return $this->trace_output;
	}
	
	function serialize(){
		return serialize($this);
	}
}