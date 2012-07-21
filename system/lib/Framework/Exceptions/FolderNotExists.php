<?php
namespace Exceptions;
class FolderNotExists extends FileException {
	function __construct($file){
		parent::__construct($file);
		$this->message = 'Folder "'. $file .'" does not exist';
	}
}