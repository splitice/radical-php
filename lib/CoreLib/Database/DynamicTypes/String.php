<?php
namespace Database\DynamicTypes;

class String implements IDynamicType {
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
		return (string)$this->value;
	}
	static function fromDatabaseModel($value,array $extra){
		return new static($value,$extra);
	}
}