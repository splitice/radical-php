<?php
namespace CLI\DaemonCommands\Internal;

abstract class StandardCommand extends BaseCommand {
	const NAME = '';
	
	function is(array $argv){
		if(isset($argv[1]) && $argv[1] == static::NAME){
			return true;
		}
		return false;
	}
}