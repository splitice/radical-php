<?php
namespace HTML\Form\Element\Select;
use HTML\Element;

class Option extends Element {
	function __construct($value,$text = null,$selected = false){
		$attributes = array();
		$attributes['value'] = $value;
		
		if(null === $text){
			$text = $value;
		}
		
		parent::__construct('option',$attributes,$text);
		
		if($selected == true){
			$this->setSelected(true);
		}
	}
	
	/**
	 * @return the $value
	 */
	public function getValue() {
		return $this->attributes['value'];
	}

	function setValue($value){
		$this->attributes['value'] = $value;
	}

	function setSelected($value){
		if($value){
			$this->attributes['selected'] = 'selected';
		}else{
			unset($this->attributes['selected']);
		}
	}
}