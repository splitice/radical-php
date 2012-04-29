<?php
namespace Database\SQL\Parts\Internal;

use Database\SQL\Internal\MergeBase;
use Database\IToSQL;

abstract class MergePartBase extends MergeBase implements IToSQL {
	function __toString(){
		return $this->toSQL();
	}
}