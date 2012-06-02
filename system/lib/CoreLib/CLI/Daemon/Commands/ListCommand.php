<?php
namespace CLI\DaemonCommands;

class ListCommand extends Internal\StandardCommand {
	const NAME = 'list';
	
	function Execute($pid,$script) {
		global $_SCRIPT_NAME;
		
		passthru('ps x | grep '.escapeshellarg($_SCRIPT_NAME).' | grep -v SCREEN | grep -v grep');
	}
}