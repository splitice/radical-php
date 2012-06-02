<?php
namespace Web\Form\Element;

class HiddenInput extends Internal\InputElement {
	function __construct($name,$value){
		parent::__construct('hidden',$name,$value);
	}
}