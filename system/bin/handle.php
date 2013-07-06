<?php
use Core\ErrorHandling;
include(__DIR__.'/../include/common.php');

Web\Page\Request::fromRequest();
$handler = ErrorHandling\Handler::Handle(function(){
	$handler = Web\Page\Router\Recognise::fromRequest();
	if(!$handler){
		$handler = new \Web\Page\Controller\Special\FileNotFound();
	}
	
	return $handler;
});
$handler->Execute($_SERVER['REQUEST_METHOD']);