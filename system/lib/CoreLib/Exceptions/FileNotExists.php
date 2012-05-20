<?php
namespace Exceptions;
class FileNotExists extends FileException {
	function __construct($file){
		parent::__construct($file);
		$this->message = 'File "'. $file .'" does not exist';
	}
}