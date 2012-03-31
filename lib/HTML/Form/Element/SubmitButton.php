<?php
namespace HTML\Form\Element;

class SubmitButton extends Button {
	function __construct($value = 'Submit'){
		parent::__construct('submit',$value);
	}
}