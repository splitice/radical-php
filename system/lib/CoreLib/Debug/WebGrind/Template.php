<?php
namespace Debug\WebGrind;

use Web\PageHandler\PageBase;

class Template extends PageBase {
	protected $template;
	protected $filename;
	
	function path($a){
		return '/lib/CoreLib/Debug/WebGrind/'.$a;
	}
	function __construct($template,$filename){
		$this->template = $template;
		$this->filename = $filename;
	}
	function GET(){
		include(__DIR__.'/templates/'.$this->template.'.phtml');
	}
	function POST(){
		$this->GET();
	}
}