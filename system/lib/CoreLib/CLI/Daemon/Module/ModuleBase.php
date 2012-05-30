<?php
namespace CLI\Daemon\Module;

abstract class ModuleBase {
	protected $autoRestart = true;
	
	abstract function Loop($parameters);
	function Execute($parameters){
		while(true){
			if($autoRestart && pcntl_fork()){
				do{
					$pid = pcntl_wait($status);
				}while($pid == -1);
			}else{
				$this->Loop($parameters);
			}
		}
	}
}