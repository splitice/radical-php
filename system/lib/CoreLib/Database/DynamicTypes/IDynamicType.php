<?php
namespace Database\DynamicTypes;

use Database\Model\ITable;

interface IDynamicType {
	public function setValue($value);
	function __toString();
	static function fromDatabaseModel($value,array $extra,ITable $model);
}