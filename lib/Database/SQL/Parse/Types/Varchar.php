<?php
namespace Database\SQL\Parse\Types;
use Basic\Validation\IValidator;

use HTML\Form\Element;

class Varchar extends ZZ_Unknown implements IValidator {
	const TYPE = 'varchar';
	
	function Validate($value){
		return (strlen($value) <= $this->size);
	}
}