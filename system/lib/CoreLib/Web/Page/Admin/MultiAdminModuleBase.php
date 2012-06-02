<?php
namespace Web\Page\Admin;

use Net\URL\Path;
use Web\Page\Handler;

abstract class MultiAdminModuleBase extends AdminModuleBase {
	private $submodule;
	function __construct(Path $url = null){
		if($url !== null)
			$this->submodule = $url->firstPathElement();
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
		$method = 'action'.$this->submodule;
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
	function toURL(){
		return parent::toURL().'/'.$this->submodule;
	}
	static function fromSub($submodule){
		$class = array_pop(explode('\\',get_called_class()));
		$url = new Path($submodule);
		return new static($url);
	}
}