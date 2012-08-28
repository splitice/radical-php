<?php
use Core\ErrorHandling\Handler;

include(__DIR__.'/../include/common.php');

$job = $argv[1];
echo "Running: ",$job,"\r\n";

//Get Arguments
$arguments = $argv;
unset($arguments[0],$arguments[1]);
$arguments = array_values($arguments);

//Do, and handle errors.
Handler::Handle(function() use($job,$arguments){
	$run = new CLI\Cron\Runner($job);
	$run->Run($arguments);
});