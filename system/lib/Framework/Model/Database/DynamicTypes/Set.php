<?php
namespace Model\Database\DynamicTypes;
use Model\Database\Model\ITable;

class Set extends DynamicType implements IDynamicType,IDynamicValidate {
	protected $value;
	protected $extra;
	protected $keys;
	
	function __construct($value, $extra, $keys){
		$this->keys = $keys;
		$this->extra = $extra;
		$this->setValue($value);
	}
	function has($name){
		return $this->value;
	}
	function validate($value){
		return in_array($value, $this->keys);
	}
	
	static function fromDatabaseModel($value,array $extra,ITable $model,$field = null){
		$keys = $model->orm->validation->request_data($model->orm->reverseMappings[$field])->getValues();
		
		return new static($value,$extra, $keys);
	}
	static function fromUserModel($value,array $extra,ITable $model){
		return static::fromDatabaseModel($value, $extra, $model);
	}
	
	function __toString(){
		if(!is_array($this->value)){
			die(var_dump($this->value));
		}
		return (string)implode(',',$this->value);
	}
	function toSQL(){
		return $this->__toString();
	}
	function setValue($value){
		if(is_string($value)){
			$this->value = explode(',', $value);
		}else{
			$this->value = $value;
		}
	}
}