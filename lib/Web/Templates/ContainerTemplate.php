<?php
namespace Web\Templates;

class ContainerTemplate extends \Web\Template {
	public $incBody = true;
	
	function __construct($name, $vars = array(), $containerFile = 'Common/container'){
		parent::__construct($containerFile,$vars);
		$this->addVarMember('TEMPLATE_FILE',static::getPath($name));
	}
}