<?php
namespace File;

class Lock {
	protected $file;
	
	function __construct(\File $file){
		$this->file = $file;
	}
	
	function Lock($opt,$block = null){
		$handle = $this->file->fopen('r');
		return flock($handle, $opt, $block);
	}
}