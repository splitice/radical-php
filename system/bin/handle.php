<?php
include(__DIR__.'/../include/common.php');

new Core\ErrorHandling\Handlers\OutputErrorHandler();

$handler = Web\Page\Router\Recognise::fromRequest();
if(!$handler){
	$handler = new \Web\Page\Controller\Special\FileNotFound();
}
$handler->Execute($_SERVER['REQUEST_METHOD']);