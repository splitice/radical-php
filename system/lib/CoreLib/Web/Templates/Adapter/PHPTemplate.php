<?php
namespace Web\Templates\Adapter;

use Web\Templates\Scope;

/**
 * The default adapter. Uses php files for templates.
 * 
 * $_ is a global in these files which provides access
 * to variables and a set of helper functions.
 * 
 * @author SplitIce
 *
 */
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