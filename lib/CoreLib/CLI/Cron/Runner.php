<?php
namespace CLI\Cron;

class Runner extends \Core\Object {
	static $__dependencies = array('lib.cli.cron.job');
	
	private $job;
	
	function __construct($job){
		$this->job = $job;
	}
	
	private function getClass(){
		$class = '\\CLI\\Cron\\Jobs\\'.$this->job;
		return $class;
	}
	
	function isValid(){
		if(class_exists($this->getClass())){
			if(oneof($this->getClass(), '\\CLI\\Cron\\Jobs\\Interfaces\\ICronJob')){
				return true;
			}
		}
		return false;
	}
	
	
	function Run(array $arguments){
		$class = $this->getClass();
		$instance = new $class();
		if($instance instanceof Jobs\Interfaces\ICronJob){
			$instance->Execute($arguments);
		}
	}
}