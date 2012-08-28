<?php
namespace Core\ErrorHandling\Handlers;

use Core\ErrorHandling\IToCode;
use Core\ErrorHandling\Errors\Internal\ErrorBase;
use Core\ErrorHandling\Errors\Internal\ErrorException;
use CLI\Console\Colors;
use CLI\Threading\Thread;
use Core\ErrorHandling\Errors;

class OutputErrorHandler extends ErrorHandlerBase {
	const CLI_START = "[%s]%s\n";
	
	function error(ErrorBase $error) {
		if($error->isFatal()){
			throw $error;
		}
	}
	function exception(ErrorException $error){
		if(\Core\Server::isCLI()){
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
			$T = Thread::current();
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
			try {
				//@todo Remove ugly hack
				$page = $error->getPage();
				while($page){
					$page = $page->GET();
				}
			}catch(\Exception $ex){
				die('Error: '.$ex->getMessage());
			}
			exit;
		}
	}
}