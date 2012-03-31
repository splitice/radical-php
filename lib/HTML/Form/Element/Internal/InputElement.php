<?php
namespace HTML\Form\Element\Internal;

abstract class InputElement extends FormElementBase {
	function __construct($type,$name,$value){
		parent::__construct('input',$name);
		$this->attributes['type'] = $type;
		$this->setValue($value);
	}
	function setValue($value){
		$this->attributes['value'] = $value;
	}
	function getValue(){
		return $this->attributes['value'];
	}
}