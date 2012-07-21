<?php
namespace Model\Database\DynamicTypes;

use Model\Database\Model\ITable;

class Stub extends String implements INullable {
	function isNull(){
		return ($this->value === null);
	}
	function getField(){
		$field = '*title';
		if($this->extra){
			$field = $this->extra[0];
		}
		return $field;
	}
	function getMode(){
		$mode = 'Standard';
		if(count($this->extra) > 1){
			$mode = $this->extra[1];
		}
		return $mode;
	}
	function getGenerator(){
		$class = '\\Net\\URL\\Stub\\Generator\\'.$this->getMode();
		return $class;
	}
	function generate($value){
		$class = static::getGenerator();
		$generated = $class::Generate($value);
		$this->setValue($generated);
		return $generated;
	}
	static function fromDatabaseModel($value,array $extra,ITable $model){
		$return = parent::fromDatabaseModel($value, $extra, $model);
		if($value === null){
			$field = $return->getField();
			$value = $model->getSQLField($field);
			$return->generate($value);
		}
		return $return;
	}
}