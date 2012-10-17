<?php
namespace Model\Database\DynamicTypes;

use Exceptions\ValidationException;
use Model\Database\Model\ITable;

class Boolean extends String implements IDynamicValidate {
	/**
	 * @param string $value
	 */
	public function setValue($value) {
		if(is_bool($value)){
			if($value){
				$value = $this->getTrueValue();
			}else{
				$value = $this->getFalseValue();
			}
			parent::setValue($value);
		}elseif(in_array($value, $this->extra)){
			parent::setValue($value);
		}else{
			throw new \Exception('Invalid value');
		}
	}
	
	function getTrueValue(){
		return $this->extra[0];
	}
	function getFalseValue(){
		return $this->extra[1];
	}
	
	function true(){
		return ($this->value == $this->getTrueValue());
	}
	function false(){
		return ($this->value == $this->getFalseValue());
	}
	function isTrue(){
		return $this->true();
	}
	function isFalse(){
		return $this->false();
	}
	function validate($value){
		return in_array($value, $this->extra);
	}
	
	static function fromUserModel($value,array $extra,ITable $model){
		if(is_bool($value)){
			if($value){
				$value = $extra[0];
			}else{
				$value = $extra[1];
			}
		}else{
			throw new ValidationException();
		}
		return static::fromDatabaseModel($value, $extra, $model);
	}
}