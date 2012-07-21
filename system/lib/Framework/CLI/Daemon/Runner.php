<?php
namespace CLI\Daemon;

class Runner extends \Core\Object {
	static $__dependencies = array('lib.cli.daemon.module');
	
	private $module;
	
	function __construct($module){
		$this->module = $module;
	}
	
	private function getClass(){
		$class = '\\CLI\\Daemon\\Module\\'.$this->module;
		return $class;
	}
	
	function isValid(){
		if(class_exists($this->getClass())){
			if(oneof($this->getClass(), '\\CLI\\Daemon\\Module\\Interfaces\\IModuleJob')){
				return true;
			}
		}
		return false;
	}
	
	
	function Run(array $arguments){
		$class = $this->getClass();
		$instance = new $class();
		if($instance instanceof Module\Interfaces\IModuleJob){
			$instance->Execute($arguments);
		}
	}
}