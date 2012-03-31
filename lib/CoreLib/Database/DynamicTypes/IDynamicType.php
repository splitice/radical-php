<?php
namespace Database\DynamicTypes;

interface IDynamicType {
	public function setValue($value);
	function __toString();
	static function fromDatabaseModel($value,array $extra);
}