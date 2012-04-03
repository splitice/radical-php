<?php
include(__DIR__.'/../include/common.php');

new ErrorHandling\Handlers\OutputErrorHandler();

$handler = Web\PageRecogniser\Recognise::fromRequest();
if(!$handler){
	$handler = new \Web\Pages\Special\FileNotFound();
}
$handler->Execute($_SERVER['REQUEST_METHOD']);