<?php
namespace CLI\DaemonCommands;

class Stop extends Internal\StandardCommand {
	const NAME = 'stop';
	
	function Execute($pid,$script) {
		global $_SCRIPT_NAME;
	
		exec('screen -D '.escapeshellarg($_SCRIPT_NAME),$lines);
		foreach($lines as $l){
			if(preg_match('#([0-9]+).'.preg_quote($_SCRIPT_NAME,'#').'#', $l, $l)){
				passthru('kill '.escapeshellarg($l[1]),$lines);
				break;
			}
		}
	}
}