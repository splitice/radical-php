<?php
namespace Web\Page\Admin;

use Web\Page\Handler\HTMLPageBase;
use Web\Page\Handler;
use Web\Template;
use Web\Templates;

abstract class AdminModuleBase extends HTMLPageBase implements Modules\IAdminModule {
	function getName(){
		return $this->getModuleName();
	}
	function getModuleName(){
		$c = ltrim(get_called_class(),'\\');
		$c = substr($c,strlen(Constants::CLASS_PATH));
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
	protected function _T($template,$vars){
		if($_POST['_admin'] == 'outer'){
			$vars['this'] = $this;
			return new Template($template,$vars,'admin');
		}
		$menu = new Menu($this->getModuleName());
		$vars['menu'] = $menu;
		return new Templates\ContainerTemplate($template,$vars,'admin');
	}
	
	function toId(){
		return 'tab-'.$this->getModuleName();
	}
}