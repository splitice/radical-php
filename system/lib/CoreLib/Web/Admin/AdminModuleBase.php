<?php
namespace Web\Admin;

use Web\PageHandler;

abstract class AdminModuleBase extends PageHandler\HTMLPageBase implements Modules\IAdminModule {
	abstract function __construct(\Net\URL\Path $url = null);
	function getName(){
		return $this->getModuleName();
	}
	function getModuleName(){
		$c = ltrim(get_called_class(),'\\');
		$c = substr($c,strlen('Web\\Admin\\Modules\\'));
		return $c;
	}
	function toURL(){
		return '/admin/'.$this->getModuleName();
	}
	function __toString(){
		return $this->getName();
	}
	static function createLink(){
		return new static();
	}
}