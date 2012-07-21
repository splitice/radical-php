<?php
namespace Model\Database\DynamicTypes;

use Model\Database\Model\ITable;

class DateTime extends \Basic\DateTime\DateTime implements IDynamicType {
	protected $extra;
	
	/**
	 * @param string $value
	 */
	public function setValue($value) {
		$this->timestamp = $value;
	}

	function __construct($value,$extra = null){
		$this->extra = $extra;
		parent::__construct($value);
	}
	function __toString(){
		return (string)$this->toSQL();
	}
	static function fromDatabaseModel($value,array $extra,ITable $model){
		if(is_int($value)){
			return new static($value);
		}
		return parent::fromSQL($value);
	}
	static function fromUserModel($value,array $extra,ITable $model){
		return static::fromDatabaseModel($value, $extra, $model);
	}
}