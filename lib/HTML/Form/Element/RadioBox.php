<?php
namespace HTML\Form\Element;

class RadioBox extends Internal\InputElement {
	function __construct($name,$value,$checked){
		parent::__construct('radio',$name,$value);
		if($checked == true){
			$this->attributes['checked'] = 'checked';
		}
	}
}