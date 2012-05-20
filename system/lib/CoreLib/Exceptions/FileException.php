<?php
namespace Exceptions;

abstract class FileException extends IOException {
	private $file;
	
	function __construct($file){
		$this->file = $file;
	}
	
	/**
	 * @return the $file
	 */
	public function getFile() {
		return $this->file;
	}
}