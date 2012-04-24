<?php
namespace Web\Templates;

class ContainerTemplate extends \Web\Template {
	const DEFAULT_CONTAINER = 'Common/container';
	
	public $incBody = true;
	protected $body;
	private $container;
	
	function __construct($name, $vars = array(), $container = 'HTML', $body = self::DEFAULT_CONTAINER){
		parent::__construct($body,$vars,$container);
		$this->body = $name;
		$this->container = $container;
	}
	
	protected function _scope(){
		return new ContainerScope($this->vars, $this->handler, $this->body, $this->container);
	}
}