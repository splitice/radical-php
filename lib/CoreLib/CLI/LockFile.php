<?php
namespace CLI;

class LockFile {
	private $file;
	
	function __construct($file){
		$this->file = $file;
		if(!$this->Check()) $this->Error();
	}
	
	function Check(){
		$lock_file = fopen ( $this->file, 'w' );
		if (flock ( $lock_file, LOCK_EX | LOCK_NB )) {
			return true;
		}
		return false;
	}
	
	function Error(){
		Output\Error::Fatal ( "An instance of ".$_SCRIPT_NAME." is already running" );
	}
}