<?php
namespace File\Format;

class ZIP extends \File\Instance {
	function __construct($filename, $create = false){
		$this->zip = new \ZipArchive;
		
		//Creatable
		$mode = 0;
		if($create){
			$mode = \ZipArchive::CREATE;
		}
		
		$res = $this->zip->open($filename, $mode);
		if(!$res){
			
		}
		
		parent::__construct($filename);
	}
	
	function Add($file){
		$this->zip->addFile($file);
	}
	
	/* SIMPLE UNRAR */
	static function unZIP($in, $to) {
		$to = realpath($to);
		
		@mkdir ( $to );
		
		//Execute
		exec ( 'unzip -q -o ' . escapeshellarg ( $in ) . ' -d ' . escapeshellarg ( $to ) );
	}
}