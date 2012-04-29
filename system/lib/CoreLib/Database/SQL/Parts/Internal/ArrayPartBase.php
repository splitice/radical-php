<?php
namespace Database\SQL\Parts\Internal;

use Basic\ArrayLib\Object\ArrayObject;

use Database\IToSQL;

abstract class ArrayPartBase extends ArrayObject implements IToSQL {
	function __construct($data = null){
		parent::__construct();
		if($data !== null) $this->_Set(null,$data);
	}
	function __toString(){
		return $this->toSQL();
	}
}