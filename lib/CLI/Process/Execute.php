<?php
namespace CLI\Process;

class Execute {
	private $command;
	private $max_execution_time;
	
	function __construct($cmd,$max_execution_time = null){
		$this->command = $cmd;
		$this->max_execution_time = $max_execution_time;
	}
	function Run(){
        $descriptorspec    = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w')
        );
        
        $pipes = array();
        
        $resource   = proc_open($this->command, $descriptorspec, $pipes, null, $_ENV);
        
        return new Process($resource,$pipes,$this->max_execution_time);
	}
}