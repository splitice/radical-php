<?php
namespace Database\ORM;

use Basic\Validation\IValidator;

class Validation {
	private $data = array();
	
	function __construct($structure){
		foreach($structure as $field=>$v){
			$type = $v->getType();
			if($type instanceof IValidator){
				$this->data[$field] = $type;
			}
		}
	}
	
	function Validate($field,$value){
		if(!isset($this->data[$field])){
			return true;//No validation
		}
		return $this->data[$field]->Validate($value);
	}
}