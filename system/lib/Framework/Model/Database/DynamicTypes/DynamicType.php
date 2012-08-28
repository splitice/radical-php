<?php
namespace Model\Database\DynamicTypes;

use Exceptions\ValidationException;

abstract class DynamicType {
	function doValidate($value, $field){
		if(!method_exists($this, 'Validate'))
			return false;
		
		if(!$this->Validate($value)) throw new ValidationException($field);
	}
}