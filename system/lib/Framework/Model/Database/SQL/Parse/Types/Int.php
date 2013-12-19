<?php
namespace Model\Database\SQL\Parse\Types;
use Basic\Validation\IValidator;

use Web\Form\Element;

class Int extends ZZ_Unknown implements IValidator {
	const TYPE = 'int';
	
	static function is($type = null){
		switch($type){
			case 'int':
			case 'smallint':
			case 'mediumint':
			case 'bigint':
				return true;
		}
		return false;
	}
	
	function validate($value){
		if(is_numeric($value)){
			return ((float)(int)$value === (float)$value) || $this->_Validate($value);
		}
		return $this->_Validate($value);
	}
}