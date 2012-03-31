<?php
namespace HTML\Form\Element;

class TextInput extends Internal\InputElement {
	function __construct($name,$value,$type = 'text'){
		parent::__construct($type,$name,$value);
	}
}