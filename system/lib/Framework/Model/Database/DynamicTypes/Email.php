<?php
namespace Model\Database\DynamicTypes;

use Exceptions\ValidationException;
use Model\Database\Model\ITable;

class Email extends String implements IDynamicValidate {
	function validate($value){
		$email = \Utility\Net\eMail::fromAddress($value);
		if(!$email)
			return false;
		return true;
	}
	function getEmail(){
		return \Utility\Net\eMail::fromAddress($this->value);
	}
}