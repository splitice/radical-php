<?php
namespace Web\Admin;

use Web\PageHandler;

abstract class AdminModuleBase extends PageHandler\HTMLPageBase implements Modules\IAdminModule {
	function getName(){
		return $this->getModuleName();
	}
	function getModuleName(){
		$c = ltrim(get_called_class(),'\\');
		$c = substr($c,strlen('Web\\Admin\\Modules\\'));
		return $c;
	}
	function getSubmodules(){
		return array();
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