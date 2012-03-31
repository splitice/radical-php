<?php
namespace ErrorHandling\Errors\Structs;

class LocationReference {
	private $file;
	private $line;
	
	function __construct($file,$line){
		$this->file = $file;
		$this->line = $line;
	}
	
	function __toString(){
		return $this->file.'@'.$this->line;
	}
	
	function toShort(){
		return \ClassLoader::pathVariblize($this->file).'@'.$this->line;
	}
}