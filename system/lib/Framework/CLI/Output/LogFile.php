<?php
namespace CLI\Output;
class LogFile {
	private $file;
	const DATE_FORMAT = 'd-m-y H:i:s.';
	
	function __construct($file){
		$this->file = fopen($file,'ct+');
		fseek($this->file,0,SEEK_END);
	}
	
	function Write($line){
		$line = rtrim($line,"\r\n")."\r\n";
		$line = date(self::DATE_FORMAT).' '.$line;
		fwrite($this->file,$line);
	}
	function ErrorCheck($line){
		$start_pos = ftell($this->file);
		self::Write($line);
		$end_pos = ftell($this->file);
		return new Log\ErrorCheck($this->file, $start_pos, $end_pos);
	}
}