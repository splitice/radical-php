<?php
namespace Database\DynamicTypes;

use Database\Model\ITable;

class DateTime extends \Basic\DateTime\DateTime implements IDynamicType {
	protected $extra;
	
	/**
	 * @param string $value
	 */
	public function setValue($value) {
		$this->timestamp = $value;
	}

	function __construct($value,$extra){
		$this->extra = $extra;
		parent::__construct($value);
	}
	function __toString(){
		return (string)$this->toSQL();
	}
	static function fromDatabaseModel($value,array $extra,ITable $model){
		return parent::fromSQL($value);
	}
}