<?php
namespace CLI\Daemon\Commands;

class Restart extends Internal\StandardCommand {
	const NAME = 'restart';
	
	function execute($pid,$script) {
		$s = new Stop();
		$s->Execute($pid, $script);
		$s = new Start();
		$s->Execute($pid, $script);
	}
}