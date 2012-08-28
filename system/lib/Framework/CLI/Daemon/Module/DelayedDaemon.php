<?php
namespace CLI\Daemon\Module;

/**
 * Run a daemon but with a delay between every itteration.
 * 
 * Extremely useful as a cron replacement either due to frequency or performance.
 * 
 * @author SplitIce
 *
 */
abstract class DelayedDaemon extends ModuleBase implements Interfaces\IModuleJob {
	/**
	 * Unless checkInteval is overwritten this is the time between loops in seconds
	 * 
	 * @var int
	 */
	const CHECK_EVERY = 300;//5 minutes
	
	/**
	 *  The function that does work, this is implemented in the main class.
	 */
	abstract protected function performWork();
	
	/**
	 * The time in seconds to wait
	 * 
	 * @return int
	 */
	protected function checkInterval(){
		return static::CHECK_EVERY;
	}
	
	/**
	 * The timestamp of the last time the daemon was allowed to work.
	 * 
	 * @var int
	 */
	private $lastAttempt;
	
	/**
	 * Attempt to do work, will return true and do the work only if the
	 * set delay time has elapsed.
	 * 
	 * @return boolean
	 */
	function Attempt(){
		if($this->lastAttempt === null || $this->lastAttempt < (time() - $this->checkInterval())){
			$this->PerformWork();
			$this->lastAttempt = time();
			return true;
		}
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see \CLI\Daemon\Module\ModuleBase::Loop()
	 */
	function loop($parameters){
		$start = time();

		$this->PerformWork();

		$this->lastAttempt = $now = time();
		$time_diff = $now - $start;

		$interval = $this->checkInterval();
		if($time_diff < $interval){
			Sleep($interval - $time_diff);
		}
	}
}