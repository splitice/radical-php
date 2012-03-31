<?php
namespace Web\PageHandler;
use Web\PageHandler as PH;

class SubRequest extends PageRequestBase {
	function __construct(IPage $page){
		parent::__construct($page);
		$this->headers = new NullHeaderManager();
	}
	function Execute($method = 'GET'){
		ob_start();
		parent::Execute($method);
		$data = ob_get_contents();
		ob_end_clean();
		
		//Pop off the stack
		PH::Pop();
		
		return $data;
	}
	static function fromURL(\Net\URL $url){
		$r = parent::fromURL($url);
		return new static($r);
	}
}