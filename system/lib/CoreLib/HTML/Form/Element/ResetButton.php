<?php
namespace HTML\Form\Element;

class ResetButton extends Button {
	function __construct($value = 'Reset'){
		parent::__construct('reset',$value);
	}
}