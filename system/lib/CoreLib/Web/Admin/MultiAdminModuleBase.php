<?php
namespace Web\Admin;

use Net\URL\Path;
use Web\PageHandler;

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