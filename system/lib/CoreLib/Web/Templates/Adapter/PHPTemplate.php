<?php
namespace Web\Templates\Adapter;

class PHPTemplate {
	private $file;
	function __construct(\File\Instance $file){
		$this->file = $file;
	}
	function Output(array $variables, $handler){
		
	}
	static function is(\File\Instance $file){
		if($file->getExtension() == 'php'){
			return true;
		}
		return false;
	}
}