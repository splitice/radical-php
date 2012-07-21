<?php
namespace Model\Database\SQL\Parts\Internal;

use Model\Database\IToSQL;

abstract class PartBase implements IToSQL {
	function __toString(){
		return $this->toSQL();
	}
}