<?php
namespace HTML\Form\Element;
use HTML\SingleTag;

class Form extends SingleTag {
	function __construct($action = null, $method = 'POST'){
		parent::__construct('form',compact('action','method'));
	}
}