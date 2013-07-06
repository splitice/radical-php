<?php
namespace Web\Templates;

class ContainerTemplate extends \Web\Template {
	const DEFAULT_CONTAINER = 'Common/container';
	
	public $incBody = true;
	protected $body;
	protected $container;
	
	function __construct($body, $vars = array(), $container = 'HTML', $name = self::DEFAULT_CONTAINER){
		parent::__construct($name,$vars,$container);
		$this->body = $body;
		$this->container = $container;
	}
	
	protected function _scope(){
		return new ContainerScope($this->vars, $this->handler, $this->body, $this->container);
	}
}