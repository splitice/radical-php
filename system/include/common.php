<?php
define ( 'DS', DIRECTORY_SEPARATOR );
$BASEPATH = rtrim(dirname ( realpath($_SERVER ["SCRIPT_NAME"].'/../') ),'/') . '/';

//Check PHP Version
if (version_compare (PHP_VERSION, '5.3.3') < 0)
{
	die ("You need PHP 5.3.3 or higher to run this script.\n");
}

$user_dir = __DIR__.DS.'..'.DS.'..'.DS;
if(!file_exists($user_include)){
	$user_include = null;
}
$user_include = $user_dir . DS . 'include' . DS;

include (__DIR__ . '/autoloader.php');
include (__DIR__ . '/functions.php');

//Config
include (__DIR__ . '/../config.php');
if($user_include && file_exists($user_dir.'config.php')){
	include($user_dir.'config.php');
}

if(isset($_SQL)){
	DB::Connect ( $_SQL );
}

Web\Session::Init ();

if($user_include && file_exists($user_include.'onload.php')){
	include($user_include.'onload.php');
}