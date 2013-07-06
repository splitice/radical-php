<?php
namespace Model\Database\SQL\Parse\Types;
use Basic\Validation\IValidator;

use Web\Form\Element;

class Set extends ZZ_Unknown implements IValidator {
	const TYPE = 'set';
	
	function getValues(){
		return array_map(function($v){ return trim($v, "'"); }, explode(',', $this->size));
	}
	function validate($value){
		return true;
		return (strlen($value) <= $this->size) || $this->_Validate($value);
	}
}