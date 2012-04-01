<?php
include(__DIR__.'/../include/common.php');

\DB::Connect($_SQL);
new ErrorHandling\Handlers\OutputErrorHandler();

/*ErrorHandling\Handler::Handle(function(){
	trigger_error('Test',E_USER_ERROR);
});

exit;*/
$handler = Web\PageRecogniser\Recognise::fromRequest();
if(!$handler){
	$handler = new \Web\Pages\Special\FileNotFound();
}
$handler->Execute($_SERVER['REQUEST_METHOD']);
