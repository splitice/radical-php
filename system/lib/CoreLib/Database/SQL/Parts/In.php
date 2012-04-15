<?php
namespace Database\SQL\Parts;

class In extends Internal\PartBase {
	protected $field;
	protected $values;
	
	function __construct($field,$values = array()){
		$this->field = $field;
		$this->values = $values;
	}
	
	function toSQL(){
		if(count($this->values) == 0){
			return 'FALSE';
		}
		return $this->field.' IN ('.\DB::A($this->values).')';
	}
}