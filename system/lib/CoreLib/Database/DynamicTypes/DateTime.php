<?php
namespace Database\DynamicTypes;

use Database\Model\ITable;

class DateTime extends \Basic\DateTime\DateTime implements IDynamicType {
	protected $value;
	protected $extra;
	
	/**
	 * @param string $value
	 */
	public function setValue($value) {
		$this->value = $value;
	}

	function __construct($value,$extra){
		$this->value = $value;
		$this->extra = $extra;
	}
	function __toString(){
		return (string)$this->toSQL();
	}
	static function fromDatabaseModel($value,array $extra,ITable $model){
		return parent::fromSQL($value);
	}
}