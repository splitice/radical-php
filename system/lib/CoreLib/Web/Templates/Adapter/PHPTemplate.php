<?php
namespace Web\Templates\Adapter;

use Web\Templates\Scope;

class PHPTemplate implements ITemplateAdapter {
	private $file;
	function __construct(\File $file){
		$this->file = $file;
	}
	function Output(Scope $_){
		global $_CONFIG;
		include($this->file);
	}
	static function is(\File $file){
		if($file->getExtension() == 'php'){
			return true;
		}
		return false;
	}
}