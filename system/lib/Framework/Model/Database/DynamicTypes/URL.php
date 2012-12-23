<?php
namespace Model\Database\DynamicTypes;

use Exceptions\ValidationException;
use Model\Database\Model\ITable;

class URL extends String implements IDynamicValidate {
	function validate($value){
		$url = \Utility\Net\URL::fromURL($value);
		if(!$url)
			return false;
		return true;
	}
	function getUrl(){
		return \Utility\Net\URL::fromURL($this->value);
	}
}