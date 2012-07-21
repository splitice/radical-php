<?php
namespace CLI\DaemonCommands;

class Restart extends Internal\StandardCommand {
	const NAME = 'restart';
	
	function Execute($pid,$script) {
		$s = new Stop();
		$s->Execute($pid, $script);
		$s = new Start();
		$s->Execute($pid, $script);
	}
}