<?php
namespace Core\ErrorHandling\Handlers;

use Core\ErrorHandling\Errors\Internal\ErrorBase;
use Core\ErrorHandling\Errors\Internal\ErrorException;
use CLI\Console\Colors;

class NullErrorHandler extends ErrorHandlerBase {
	function Error(ErrorBase $error) {
	}
	function Exception(ErrorException $error){
	}
	function isNull(){
		return true;
	}
}