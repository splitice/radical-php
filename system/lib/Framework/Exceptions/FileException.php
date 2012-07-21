<?php
namespace Exceptions;

abstract class FileException extends IOException {
	private $theFile;
	
	function __construct($file){
		$this->theFile = $file;
	}
	
	/**
	 * @return the $theFile
	 */
	public function getTheFile() {
		return $this->theFile;
	}
}