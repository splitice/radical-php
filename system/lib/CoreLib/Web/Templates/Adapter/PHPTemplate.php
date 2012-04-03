<?php
namespace Web\Templates\Adapter;

use Web\Templates\Scope;

class PHPTemplate {
	private $file;
	function __construct(\File\Instance $file){
		$this->file = $file;
	}
	function Output(Scope $_){
		global $_CONFIG;
		include($this->file);
	}
	static function is(\File\Instance $file){
		if($file->getExtension() == 'php'){
			return true;
		}
		return false;
	}
}