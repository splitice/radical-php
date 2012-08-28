<?php
namespace Core\ErrorHandling\Handlers;

use Core\ErrorHandling\Errors\Internal\ErrorBase;
use Core\ErrorHandling\Errors\Internal\ErrorException;
use CLI\Console\Colors;

class NullErrorHandler extends ErrorHandlerBase {
	function error(ErrorBase $error) {
	}
	function exception(ErrorException $error){
	}
	function isNull(){
		return true;
	}
}