<?php
namespace Web\Page\Admin;

use Utility\Net\URL\Path;
use Web\Page\Handler;
use Web\Templates;

abstract class MultiAdminModuleBase extends AdminModuleBase {
	private $submodule;
	function __construct(Path $url = null){
		if($url !== null){
			$this->submodule = $url->firstPathElement();
			$url->removeFirstPathElement();
		}
	}
	function getSubmodules(){
		$r = new \ReflectionClass(get_called_class());
		foreach($r->getMethods() as $method){
			$method = $method->getName();
			if(substr($method,0,6)=='action'){
				$name = substr($method,6);
				$modules[$name] = static::fromSub($name);
			}
		}
		return $modules;
	}
	function GET($data = array()){
		if(!$this->submodule){
			$method = 'index';
		}else{
			$method = 'action'.$this->submodule;
		}
		if(method_exists($this,$method)){
			return $this->$method($data);
		}
		throw new \Exception('Admin submodule '.$this->submodule.' doesnt exist');
	}
	function POST(){
		return $this->GET($data);
	}
	function __toString(){
		if($this->submodule === null) return parent::__toString();
		return $this->submodule;
	}
	protected function _T($template,$vars){
		if($_POST['_admin'] == 'outer'){
			$menu = new SubMenu($this,$this->submodule);
			$vars['menu'] = $menu;
			return new Templates\ContainerTemplate($template,$vars,'admin','Common/subwrapper');
		}else {
			if($_POST['_admin'] == 'inner')
				$_POST['_admin'] = 'outer';
			return parent::_T($template, $vars);
		}
	}
	function toURL(){
		return parent::toURL().'/'.$this->submodule;
	}
	function toId(){
		return parent::toId().'-'.$this->submodule;
	}
	static function fromSub($submodule){
		$class = array_pop(explode('\\',get_called_class()));
		$url = new Path($submodule);
		return new static($url);
	}
}