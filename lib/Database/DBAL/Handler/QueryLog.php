<?php
namespace Database\DBAL\Handler;

class QueryLog {
	private $queries = array();
	private $backtraces = array();
	private $explain = false;
	
	function __construct($explain = false){
		$this->explain = $explain;
	}
	
	function addQuery($sql) {
		if(class_exists('Server')){
			if ($this->explain) {
				$this->queries[] = $sql;
				$this->backtraces[] = debug_backtrace(false);
			}
		}
	}
	
	function getQueries(){
		return $this->queries;
	}
	function getBacktrace($n){
		return $this->backtraces[$n];
	}
}