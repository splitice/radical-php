<?php
namespace Web\PageHandler;
use \ErrorHandling\Errors\Internal\ErrorException;
use \ErrorHandling\Handler;

abstract class PageBase extends \Core\Object implements IPage {
	function can($method){
		return method_exists($this,$method);
	}
	
	function Execute($method = 'GET'){
		$pageHandler = new PageRequest($this);
		\ErrorHandling\Handler::Handle(array($pageHandler,'Execute'),array($method));
	}
}
