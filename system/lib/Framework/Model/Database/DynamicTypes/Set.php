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
		return in_array($name, $this->value);
	}
	function validate($value){
		if(!$value)
			return true;
		
		foreach(explode(',',$value) as $v){
			if(!in_array($v, $this->keys)){
				return false;
			}
		}
		return true;
	}
	
	function getSet(){
		return $this->value;
	}
	
	function set($name, $value){
		$found = array_search($name, $this->value);
		if($value && $found === false){
			$this->value[] = $name;
		}else if(!$value && $found !== false){
			unset($this->value[$found]);
		}
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
		if(count($value) == 0){
			$this->value = array();
		}elseif(is_string($value)){
			if(empty($value)){
				$this->value = array();
			}else{
				$this->value = explode(',', $value);
			}
		}else{
			$this->value = $value;
		}
	}
}