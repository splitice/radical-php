<?php
namespace Model\Database\DynamicTypes;

use Model\Database\IToSQL;
use Model\Database\Model\ITable;

interface IDynamicType extends IToSQL {
	public function setValue($value);
	function __toString();
	static function fromDatabaseModel($value,array $extra,ITable $model);
	static function fromUserModel($value,array $extra,ITable $model);
}