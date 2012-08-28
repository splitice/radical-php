<?php
namespace Web\Page\Admin;

use Web\Page\Controller\Special\Redirect;

use Utility\Net\URL\Path;
use Web\Page\Handler;
use Web\Templates;

abstract class MultiAdminModuleBase extends AdminModuleBase {
	protected $submodule;
	function __construct(Path $url = null){
		if($url !== null){
			$this->submodule = $url->firstPathElement();
			$url->removeFirstPathElement();
		}
	}
	function getSubmodules($inclIndex = true){
		$modules = array();
		//Default module
		if($inclIndex && method_exists($this, 'index')){
			$modules['Overview'] = static::fromSub(null);
		}
		
		//Submodules
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
	function gET($data = array()){
		if(!$this->submodule){
			$method = 'index';
			if(!method_exists($this,$method)){
				$sub = array_shift($this->getSubmodules(false));
				$r = new static(new Path($sub));
				return new Redirect($r->toURL());
			}
		}else{
			$method = 'action'.$this->submodule;
		}
		if(method_exists($this,$method)){
			return $this->$method($data);
		}
		throw new \Exception('Admin submodule '.$this->submodule.' doesnt exist');
	}
	function pOST(){
		return $this->GET($_POST);
	}
	function __toString(){
		if($this->submodule === null) return parent::__toString();
		return $this->submodule;
	}
	protected function _T($template,$vars){
		if(Request::Context() == Request::CONTEXT_OUTER){
			$menu = new SubMenu($this,$this->submodule);
			$vars['this'] = $this;
			$vars['menu'] = $menu;
			return new Templates\ContainerTemplate($template,$vars,'admin','Common/subwrapper');
		}else {
			if(Request::Context() == Request::CONTEXT_INNER)
				Request::Context(Request::CONTEXT_OUTER);
			
			return parent::_T($template, $vars);
		}
	}
	function toURL(){
		return parent::toURL().'/'.$this->submodule;
	}
	function toId(){
		$id = parent::toId();
		if($this->submodule && Request::Context() == Request::CONTEXT_OUTER){
			$id .= '-'.$this->submodule;
		}
		return $id;
	}
	static function fromSub($submodule){
		$class = array_pop(explode('\\',get_called_class()));
		$url = new Path($submodule);
		return new static($url);
	}
}