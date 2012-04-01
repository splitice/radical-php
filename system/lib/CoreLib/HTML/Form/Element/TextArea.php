<?php
namespace HTML\Form\Element;

class TextArea extends Internal\FormElementBase {
	function __construct($name,$value){
		parent::__construct('textarea',$name);
		$this->inner = $value;
	}
}