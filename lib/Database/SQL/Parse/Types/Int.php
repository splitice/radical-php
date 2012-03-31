<?php
namespace Database\SQL\Parse\Types;
use Basic\Validation\IValidator;

use HTML\Form\Element;

class Int extends ZZ_Unknown implements IValidator {
	const TYPE = 'int';
	
	static function is($type){
		switch($type){
			case 'int':
			case 'smallint':
			case 'mediumint':
			case 'bigint':
				return true;
		}
		return false;
	}
	
	function Validate($value){
		if(is_numeric($value)){
			return ((float)(int)$value === (float)$value);
		}
		return false;
	}
}