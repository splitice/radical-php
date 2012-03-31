<?php
namespace Database\DynamicTypes;

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
	static function fromDatabaseModel($value,array $extra){
		return parent::fromSQL($value);
	}
}