<?php
namespace Web\Templates;

use HTML\Form\Builder\FormBuilder;

class Scope {
	public $form;
	public $vars = array();
	public $handler;
	
	function __construct(array $vars,$handler){
		$this->form = new FormBuilder();
		$this->vars = $vars;
		$this->handler;
	}
	
	function bind($var){
		foreach($var as $k=>$v){
			$this->$k = $v;
		}
	}
}