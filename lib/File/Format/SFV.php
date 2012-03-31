<?php
namespace File\Format;

class SFV extends \File\Instance {
	private $entries = array();
	
	function __construct($file){
		parent::__construct($file);
		$this->Parse();
	}
	
	private function hashFile($filename){
		return hash_file('crc32b', $filename);
	}
	
	private function Parse(){
		$entries = array();
		
		$file_contents = $this->Contents();
		
		foreach(explode("\n",$file_contents) as $line){
			$line = trim($line);
			if(!$line){
				continue;
			}
			
			if($line{0} == ';'){
				continue;//Comment
			}
			
			$space = strrpos($line,' ');
			if(!$space){
				continue;//Invalid line
			}
			
			$filename = substr($line,0,$space);
			$hash = substr($line,$space+1);
			
			if(strlen($hash) != 8){
				continue;//Invalid
			}
			
			
			$entries[$line] = $hash;
		}
		
		$this->entries = $entries;
	}
	
	function Validate(){
		$ok = true;
		foreach($this->entries as $file=>$hash){
			$full_file = dirname($this->file).DIRECTORY_SEPARATOR.$file;
			if(file_exists($full_file)){
				if($this->hashFile($full_file) != $hash){
					$ok = false;
				}
			}
		}
		return $ok;
	}
}