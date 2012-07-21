<?php
namespace Exceptions;

class ValidationException extends \Exception {
	protected $field;
	function __construct($field = null){
		$this->field = $field;
		$message = 'A validation Exception occured';
		if($field){
			$message .= ' with '.$field;
		}
		parent::__construct($message);
	}
	
	/**
	 * @return the $field
	 */
	public function getField() {
		return $this->field;
	}
}