<?php
namespace Model\Database\SQL\Parts\Internal;

use Model\Database\SQL\Internal\MergeBase;
use Model\Database\IToSQL;

abstract class MergePartBase extends MergeBase implements IToSQL {
	function __toString(){
		return $this->toSQL();
	}
}