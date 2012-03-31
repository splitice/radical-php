<?php
namespace ErrorHandling\Handlers;

use ErrorHandling\IToCode;
use ErrorHandling\Errors\Internal\ErrorBase;
use ErrorHandling\Errors\Internal\ErrorException;
use CLI\Console\Colors;
use CLI\Thread;
use ErrorHandling\Errors;

class OutputErrorHandler extends Internal\ErrorHandlerBase {
	const CLI_START = "[%s]%s\n";
	
	function Error(ErrorBase $error) {
		if($error->isFatal()){
			throw $error;
		}
	}
	function Exception(ErrorException $error){
		if(\Server::isCLI()){
			$c = Colors::getInstance();
			
			//Code
			if($error instanceof IToCode){
				$code = $error->toCode();
			}else{
				if($error->isFatal()){
					$code = $c->getColoredString('FATAL','red');
				}else{
					$code = $c->getColoredString('ERROR','light_red');
				}
			}
			
			//Format Output
			$message = $error->getMessage();
			if($message{0} != '['){
				$message = ' '.$message;
			}
			$output = sprintf(static::CLI_START,$code,$message);
			
			//If Threaded include ThreadID
			$T = Thread::$self;
			if($T){//If threading
				if($T->parent || count($T->children)){
					$output = '['.$c->getColoredString('#'.$T->getId(),'cyan').']'.$output;
				}
			}
			
			//Output it
			\CLI\Console\Colors::getInstance()->Output($output);
			
			//OB
			if(ob_get_level()) ob_flush();
		}else{
			if(ob_get_level()) ob_end_clean();
			$error->getPage()->Execute();
			exit;
		}
	}
}