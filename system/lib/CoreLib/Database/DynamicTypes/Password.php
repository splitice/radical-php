<?php
namespace Database\DynamicTypes;

class Password extends String {
	function getAlgo(){
		$algo = 'Raw';
		if($this->extra){
			$algo = $this->extra[0];
		}
		return '\\Basic\\Cryptography\\'.$algo;
	}
	function Compare($with){
		$class = $this->getAlgo();
		return $class::Compare($with,$this->value);
	}
	function setValue($value){
		$class = $this->getAlgo();
		parent::setValue($class::Hash($value));
	}
}