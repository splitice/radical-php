<?php
define ( 'DS', DIRECTORY_SEPARATOR );
$BASEPATH = $_SERVER['DOCUMENT_ROOT'] . DS;

//Check PHP Version
if (version_compare (PHP_VERSION, '5.3.3') < 0)
{
	die ("You need PHP 5.3.3 or higher to run this script.\n");
}

//Path to user directory
$user_dir = $BASEPATH.'app'.DS;
if(!($user_dir = realpath($user_dir))){
	die('Application directory not provided.');
}
$user_include = $user_dir . DS . 'include' . DS;

include (__DIR__ . '/autoloader.php');
include (__DIR__ . '/functions.php');

//Config
include ($user_dir . '/config.php');
if($user_include && file_exists($user_dir.'config.php')){
	include($user_dir.'config.php');
}

//Connect SQL if used
if(isset($_SQL)){
	DB::Connect ( $_SQL );
}

Web\Session::Init ();

if($user_include && file_exists($user_include.'onload.php')){
	include($user_include.'onload.php');
}