<?php
namespace CLI;
class DaemonManagement {
	static function Run($pid_file,$script){
		global $argv,$_SCRIPT_NAME;
		if(isset($argv[1])){
			$pid = @file_get_contents($pid_file);
			
			$commands = \ClassLoader::getNSExpression('\\CLI\\DaemonCommands\\*');
			foreach($commands as $c){
				$c = new $c;
				if($c->is($argv)){
					$c->Execute();
					break;
				}
			}
		}
	}
	static function WritePID($file){
		file_put_contents($file,(string)getmypid());
	}
}