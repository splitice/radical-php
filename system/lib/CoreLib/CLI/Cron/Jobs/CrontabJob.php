<?php
namespace CLI\Cron\Jobs;

use Utility\Cache\PooledCache;

abstract class CrontabJob extends \Core\Object implements Interfaces\ICronJob {
	abstract function getInterval();
	abstract protected function _Execute(array $arguments);
	
	private function getTime(){
		switch($this->getInterval()){
			case 'minutely':
				$time = 60;
			case 'hourly':
				$time *= 60;
			case 'daily':
				$time *= 60;
			case 'weekly':
				$time *= 7;
		}
		return $time;
	}
	
	function Execute(array $arguments){
		$key = '__cron__'.$this->getName();//because pool isnt working for file
		$fileCache = PooledCache::Get('cron', 'FileCache');
		$lastExecute = $fileCache->Get($key);
		$fileCache->Set($key,time());
		die(var_dump($lastExecute));
		$lastWantTo = time() - $this->getTime();
		if(!$lastExecute || $lastExecute < $lastWantTo){
			$this->_Execute($arguments);
			$fileCache->Set($key,time());
		}else{
			die('its not time');
		}
	}
}