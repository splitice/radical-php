<?php
namespace Model\Database\DynamicTypes;

use Exceptions\ValidationException;

//TODO array based arithmatic
//TODO gmp support (optional)
class Decimal extends String implements IDynamicValidate {
	function add($a){
		
	}
	function subtract($a){
		
	}
	function multiply($a){
		
	}
	function divide($a){
		
	}
	
	function Validate($value){
		if(is_float($value) || is_int($value)) return true;
		if(is_string($value)){
			$value = explode('.',$value);
			if(count($value) == 1)
				if(is_numeric($value[0])) return true;
			else if(count($value) == 2)
				if(is_numeric($value[0]) && is_numeric($value[1])) return true;
		}
	}
	function DoValidate($value,$field){
		if(!$this->Validate($value)) throw new ValidationException($field);
	}
	
	function getValue($asFloat = false){
		if($asFloat) return (float)$this->value;
		
		return explode('.',$this->value);
	}
}