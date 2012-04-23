<?php
namespace Database\DynamicTypes;

class Boolean extends String {
	function getTrueValue(){
		return $this->extra[0];
	}
	function getFalseValue(){
		return $this->extra[1];
	}
	function true(){
		return ($this->value == $this->getTrueValue());
	}
	function false(){
		return ($this->value == $this->getFalseValue());
	}
}