<?php
namespace Model\Database\DynamicTypes;

use Model\Database\Model\ITable;

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
	function toSQL(){
		return $this->value;
	}
	static function fromDatabaseModel($value,array $extra,ITable $model){
		//DEVELOPER NOTE
		//$model should never be passed onto the object unless its by weakref
		//or you know what you are doing! Doing so will create a recursive reference
		//which will result in a memory leak. This will be cleaned up by the Garbage
		//Collector eventually but in a heavily used system like this it can waste
		//alot of memory before then.
		
		return new static($value,$extra);
	}
}