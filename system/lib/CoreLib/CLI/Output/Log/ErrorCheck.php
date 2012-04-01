<?php
namespace CLI\Output\Log;

class ErrorCheck{
	private $fh;
	private $start;
	private $end;
	
	function __construct($fh,$start,$end){
		$this->fh = $fh;
		$this->start = $start;
		$this->end = $end;
	}
	function OK(){
		fseek($this->fh,$this->end);
		$data = fread($this->fh,10000000);
		fseek($this->fh,0);
		ftruncate($this->fh, $this->start);
		fseek($this->fh,0,SEEK_END);
		if($data){
			fwrite($this->fh,$data);
		}
	}
}