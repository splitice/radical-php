<?php
namespace CLI\DaemonCommands;

class Start extends Internal\StandardCommand {
	const NAME = 'start';
	
	function Execute($pid,$script) {
		global $_SCRIPT_NAME;
		
		$line = false;
		if($pid){
			$cmd = 'pgrep -P '.escapeshellarg($pid);
			$line = trim(exec($cmd));
		}
		if($line){
			echo $_SCRIPT_NAME." already running\r\n";
		}else{
			$cmd = 'screen -dmS '.escapeshellarg($_SCRIPT_NAME).' php5 '.escapeshellarg($script);
			exec($cmd);
		}
	}
}