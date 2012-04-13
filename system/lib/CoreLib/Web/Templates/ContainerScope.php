<?php
namespace Web\Templates;

class ContainerScope extends Scope {
	protected $body;
	
	function __construct(array $vars,$handler,$body){
		$this->body = $body;
		parent::__construct($vars,$handler);
	}
	function body(){
		return $this->incl($this->body);
	}
}