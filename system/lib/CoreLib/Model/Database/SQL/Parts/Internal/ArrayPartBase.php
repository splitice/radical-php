<?php
namespace Model\Database\SQL\Parts\Internal;

use Basic\Arr\Object\CollectionObject;
use Model\Database\IToSQL;

abstract class ArrayPartBase extends CollectionObject implements IToSQL {
	function __construct($data = null){
		parent::__construct();
		if($data !== null) $this->_Set(null,$data);
	}
	function __toString(){
		return $this->toSQL();
	}
}