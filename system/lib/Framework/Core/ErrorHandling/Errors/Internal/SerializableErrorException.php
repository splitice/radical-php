<?php
namespace Core\ErrorHandling\Errors\Internal;
use Web\Page\Controller;
use Core\ErrorHandling\IErrorException;

class SerializableErrorException implements IErrorException {
	protected $heading;
	protected $fatal = false;
	protected $trace_output;
	protected $message;
	protected $class;
	
	function __construct(ErrorException $ex){
		$this->heading = $ex->getHeading();
		$this->fatal = $ex->isFatal();
		$this->message = $ex->getMessage();
		if(method_exists($ex,'getTraceOutput')){
			$this->trace_output = $ex->getTraceOutput();
		}else{
			$this->trace_output = $ex->getTraceAsString();
		}
		$this->class = get_class($ex);
	}
	
	function getClass(){
		return $this->class;
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