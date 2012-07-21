<?php
namespace Model\Database\DynamicTypes;

class Currency extends Decimal {
	function __toString(){
		return number_format($this->value,2);
	}
}