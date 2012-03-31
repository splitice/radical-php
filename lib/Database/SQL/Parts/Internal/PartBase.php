<?php
namespace Database\SQL\Parts\Internal;

use Database\IToSQL;

abstract class PartBase implements IToSQL {
	function __toString(){
		return $this->toSQL();
	}
}