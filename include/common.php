<?php
define ( 'DS', DIRECTORY_SEPARATOR );
$BASEPATH = rtrim(dirname ( realpath($_SERVER ["SCRIPT_NAME"].'/../') ),'/') . '/';

//Check PHP Version
if (version_compare (PHP_VERSION, '5.3.0b1-dev') < 0)
{
	die ("You need PHP 5.3 or higher to run this script.\n");
}

$user_include = __DIR__.DS.'..'.DS.'user_include'.DS;
if(!file_exists($user_include)){
	$user_include = null;
}

include (__DIR__ . '/autoloader.php');
include (__DIR__ . '/functions.php');
if($user_include && file_exists($user_include.'before_config.php')){
	include($user_include.'before_config.php');
}
include (__DIR__ . '/../config.php');

DB::Connect ( $_SQL );

Web\Session::Init ();

if($user_include && file_exists($user_include.'onload.php')){
	include($user_include.'onload.php');
}