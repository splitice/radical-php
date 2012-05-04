<?php
namespace Web\Templates;

class ContainerScope extends Scope {
	protected $body;
	protected $container;
	
	function __construct(array $vars,$handler,$body,$container){
		$this->body = $body;
		$this->container = $container;
		parent::__construct($vars,$handler);
	}
	function body(){
		return $this->incl($this->body,$this->container);
	}
	function bodyName(){
		return $this->body;
	}
}