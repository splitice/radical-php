<?php
namespace Model\Database\DynamicTypes;

//TODO array based arithmatic
//TODO gmp support (optional)
class Decimal extends String {
	function add($a){
		
	}
	function subtract($a){
		
	}
	function multiply($a){
		
	}
	function divide($a){
		
	}
	
	function getValue($asFloat = false){
		if($asFloat) return (float)$this->value;
		
		return explode('.',$this->value);
	}
}