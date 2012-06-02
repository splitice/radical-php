<?php
namespace Database\DynamicTypes;

use Database\IToSQL;
use Database\Model\ITable;

interface IDynamicType extends IToSQL {
	public function setValue($value);
	function __toString();
	static function fromDatabaseModel($value,array $extra,ITable $model);
}