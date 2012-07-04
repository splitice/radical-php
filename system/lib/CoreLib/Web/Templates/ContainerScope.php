<?php
namespace Web\Templates;

use Web\Page\Handler\IPage;

/**
 * Extra scope functions for templates based off
 *  ContainerTemplate for including the body of
 *  the container.
 * 
 * @author SplitIce
 *
 */
class ContainerScope extends Scope {
	protected $body;
	protected $container;
	
	function __construct(array $vars,IPage $handler = null,$body,$container){
		$this->body = $body;
		$this->container = $container;
		parent::__construct($vars,$handler);
	}
	/**
	 * Include the body template
	 */
	function body(){
		return $this->incl($this->body,$this->container);
	}
	/**
	 * Get the name of the body
	 */
	function bodyName(){
		return $this->body;
	}
}