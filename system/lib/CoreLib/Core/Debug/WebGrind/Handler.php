<?php
namespace Debug\WebGrind;

use Web\Template;
use Web\PageHandler\PageBase;

class Handler extends PageBase {
	protected $template;
	protected $filename;
	
	function __construct($template,$filename){
		$this->template = $template;
		$this->filename = $filename;
	}
	function GET(){
		$template = new Template($this->template,array(),'webgrind');
		$template->addVarMember('filename', $this->filename);
		return $template;
	}
	function POST(){
		$this->GET();
	}
}