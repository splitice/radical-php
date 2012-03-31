<?php
include(__DIR__.'/../include/common.php');

$job = $argv[1];

//Get Arguments
$arguments = $argv;
unset($arguments[0],$arguments[1]);
$arguments = array_values($arguments);

$run = new CLI\Cron\Runner($job);
$run->Run($arguments);