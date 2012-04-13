<?php
namespace Web\Templates;

class ContainerTemplate extends \Web\Template {
	public $incBody = true;
	protected $body;
	
	function __construct($name, $vars = array(), $container, $body = 'Common/container'){
		parent::__construct($body,$vars);
		$this->body = $name;
	}
	
	protected function _scope(){
		return new ContainerScope($this->vars, $this->handler, $this->body);
	}
}