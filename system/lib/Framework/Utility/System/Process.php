<?php
namespace Utility\System;
use Basic\Stream;

class Process {
	const STDIN = 0;
	const STDOUT = 1;
	const STDERR = 2;
	
	private $resource;
	private $pipes = array();
	private $max_execution_time;
	private $start_time;
	
	function __construct($resource,$pipes,$max_execution_time){
		$this->resource = $resource;
		$this->pipes = $pipes;
		$this->start_time = mktime();
	}
	
	function isRunning() {
		$status = proc_get_status($this->resource);
		return (bool)$status["running"];
	}
	
	function isOverTime() {
		if(!$this->max_execution_time)
			return false;
		
		return ($this->start_time+$this->max_execution_time<mktime());
	}
	
	function write($data,$stream = self::STDIN){
		$stream = $this->pipes[$stream];
		return fwrite($stream, $data);
	}
	
	function read($stream = self::STDOUT){
		$ret = '';
		$stream = array($this->pipes[$stream]);
		stream_set_blocking($stream[0],true);
		$nr = array();
		while(stream_select($stream, $nr, $nr, 0)){
			$data = fread($stream[0],1);
			if(strlen($data) == 0) return $ret;
			$ret .= $data;
		}
		return $ret;
	}
	
	function readAll($stream = self::STDOUT){
		$stream = $this->pipes[$stream];
		$stream_reader = new Stream\Reader($stream);
		return $stream_reader->ReadAll();
	}
	
	function close(){
		proc_terminate($this->resource);
	}
}
