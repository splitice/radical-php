<?php
define ( 'DS', DIRECTORY_SEPARATOR );

//Work out base path
if(!isset($BASEPATH)){
	//Attempt to compute basepath
	if($_SERVER['SCRIPT_FILENAME']){
		$BASEPATH = dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME'])));
		if(!isset($WEBPATH)){
			if(isset($_SERVER['DOCUMENT_ROOT'])){
				$dr = rtrim($_SERVER['DOCUMENT_ROOT'],DS);
				$sDR = strlen($dr);
				$WEBPATH = '';
				if(substr($BASEPATH, 0, $sDR) == $dr){
					$WEBPATH = substr($BASEPATH,$sDR);
					if($WEBPATH){
						$WEBPATH = rtrim(str_replace(DS,'/', $WEBPATH),'/');
					}
				}
				unset($dr,$sDR);
			}
		}
	}else if($_SERVER['DOCUMENT_ROOT']){
		$BASEPATH = $_SERVER['DOCUMENT_ROOT'];
	}elseif(php_sapi_name() == 'cli'){
		if(isset($argv) && isset($argv[0])){
			$ap = $argv[0];
			$BASEPATH = dirname(dirname(dirname($ap)));
			unset($ap);
		}else{
			$BASEPATH = getcwd();//We guess
		}
	}else{	
		$BASEPATH = realpath(__DIR__ . DS . '..'. DS . '..');
	}
	$BASEPATH .= DS;
}
if(!isset($WEBPATH)) $WEBPATH = '';

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
	die('Application directory not provided. Base: '.$BASEPATH);
}
$application_include = $application_dir . DS . 'include' . DS;

//Common includes
include (__DIR__ . '/autoloader.php');
include (__DIR__ . '/functions.php');

//GPC
if (get_magic_quotes_gpc()) {
	include (__DIR__ . '/magicgpc.php');
}

//Config
if($application_include && file_exists($application_dir.DS.'config.php')){
	include($application_dir.DS.'config.php');
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