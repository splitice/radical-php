<?php
namespace Web\Form\Element;

class ResetButton extends Button {
	function __construct($value = 'Reset'){
		parent::__construct($value,'reset');
	}
}