<?php
namespace CLI\Daemon\Module;

abstract class DelayedDaemon extends ModuleBase implements Interfaces\IModuleJob {
	const CHECK_EVERY = 1000;
	
	abstract protected function PerformWork();
	
	protected function checkInterval(){
		return static::CHECK_EVERY;
	}
	
	private $lastAttempt;
	function Attempt(){
		if($this->lastAttempt === null || $this->lastAttempt < (time() - $this->checkInterval())){
			$this->PerformWork();
			$this->lastAttempt = time();
			return true;
		}
		return false;
	}
	
	function Loop($parameters){
		$start = time();

		$this->PerformWork();

		$now = time();
		$time_diff = $now - $start;

		$interval = $this->checkInterval();
		if($time_diff < $interval){
			Sleep($interval - $time_diff);
		}
	}
}