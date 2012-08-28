<?php
namespace Model\Database\SQL\Parse\Types;
use Basic\Validation\IValidator;

use Web\Form\Element;

class Varchar extends ZZ_Unknown implements IValidator {
	const TYPE = 'varchar';
	
	function validate($value){
		return (strlen($value) <= $this->size) || $this->_Validate($value);
	}
}