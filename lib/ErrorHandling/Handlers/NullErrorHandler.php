<?php
namespace ErrorHandling\Handlers;
use ErrorHandling\Errors\Internal\ErrorBase;
use ErrorHandling\Errors\Internal\ErrorException;
use CLI\Console\Colors;

class NullErrorHandler extends Internal\ErrorHandlerBase {
	function Error(ErrorBase $error) {
	}
	function Exception(ErrorException $error){
	}
	function isNull(){
		return true;
	}
}