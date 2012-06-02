<?php
namespace Web\Page\Handler;
use \ErrorHandling\Errors\Internal\ErrorException;
use \ErrorHandling\Handler;

abstract class PageBase extends \Core\Object implements IPage {
	function can($method){
		return method_exists($this,$method);
	}
	
	function Execute($method = 'GET'){
		$request = new PageRequest($this);
		\ErrorHandling\Handler::Handle(array($request,'Execute'),array($method));
	}
}
