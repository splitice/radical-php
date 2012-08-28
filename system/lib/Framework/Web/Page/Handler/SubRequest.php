<?php
namespace Web\Page\Handler;

use Web\Page\Cache\NullCacheManager;
use Web\Page\Handler as PH;

class SubRequest extends PageRequestBase {
	function __construct(IPage $page){
		parent::__construct($page);
		$this->headers = new NullHeaderManager();
		$this->cache = new NullCacheManager();
	}
	function execute($method = 'GET'){
		ob_start();
		parent::Execute($method);
		$data = ob_get_contents();
		ob_end_clean();
		
		//Pop off the stack
		PH::Pop();
		
		return $data;
	}
	static function fromURL(\Utility\Net\URL $url){
		$r = parent::fromURL($url);
		return new static($r);
	}
}