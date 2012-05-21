<?php
define ( 'DS', DIRECTORY_SEPARATOR );

//Attempt to compute basepath
if($_SERVER['DOCUMENT_ROOT']){
	$BASEPATH = $_SERVER['DOCUMENT_ROOT'];
}elseif(php_sapi_name() == 'cli'){
	if(isset($argv) && isset($argv[0])){
		$BASEPATH = dirname(dirname(dirname($argv[0])));
	}else{
		$BASEPATH = getcwd();//We guess
	}
}else{	
	$BASEPATH = realpath(__DIR__ . DS . '..');
}
$BASEPATH .= DS;

//Check PHP Version
if (version_compare (PHP_VERSION, '5.3.3') < 0)
{
	//A basic error as we cant rely on any further
	//of the script to be lesser version compatible
	die ("You need PHP 5.3.3 or higher to run this script.\n");
}

//Path to user directory
$application_dir = $BASEPATH.'app'.DS;
if(!($application_dir = realpath($application_dir))){
	die('Application directory not provided.');
}
$application_include = $application_dir . DS . 'include' . DS;

//Common includes
include (__DIR__ . '/autoloader.php');
include (__DIR__ . '/functions.php');

//Config
include ($application_dir . '/config.php');
if($application_include && file_exists($application_dir.'config.php')){
	include($application_dir.'config.php');
}

//Connect SQL if used
if(isset($_SQL)){
	DB::Connect ( $_SQL );
}

//Session Init
if(php_sapi_name() != 'cli')
	Web\Session::Init ();

//Application onload hook
if($application_include && file_exists($application_include.'onload.php')){
	include($application_include.'onload.php');
}

//Cleanup
unset($application_include, $application_dir);