<?php
namespace Utility\File;

class Lock {
	protected $file;
	protected $lock;
	
	function __construct(\File $file){
		$this->file = $file;
	}
	
	function release(){
		fclose($this->lock);
	}
	
	function lock($mode = LOCK_EX,$block = true){
		if($this->lock){
			return flock($handle, $mode, $block);
		}
		$handle = $this->file->fopen('r');
		$status = flock($handle, $mode, $block);
		$this->lock = $handle;
		return $status;
	}
	
	function check($mode = LOCK_EX){
		$handle = $this->file->fopen('r');
		$status = flock($handle, $mode, $block);
		fclose($handle);
		return $status;
	}
}