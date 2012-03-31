<?php
use Basic\Structs\LoginDetails;
error_reporting(E_ALL);
include(__DIR__.'/../include/common.php');

$d = new Core\Deployment\Process();
$d->Execute(new Core\Deployment\Remote\FTP(new LoginDetails('webuser', 'webuser'),'127.0.0.1','/fgv2/'));


exit;

$_CONFIG_FILES = array('config.php','include/config.php');

function ListDir($expr, $recursive = false) {
	if ($recursive) {
		$items = glob ( $expr . '/*' );

		for($i = 0; $i < count ( $items ); $i ++) {
			if (is_dir ( $items [$i] )) {
				$add = glob ( $items [$i] . '/*' );
				$items = array_merge ( $items, $add );
			}
		}

		foreach($items as $k=>$v){
			if(is_dir($v)){
				unset($items[$k]);
			}else{
				$items[$k] = realpath($v);
			}
		}

		return $items;
	} else {
		return glob ( $expr );
	}
}

$config_file = null;
foreach($_CONFIG_FILES as $c){
	$c = __DIR__.DIRECTORY_SEPARATOR.$c;
	if(file_exists($c)){
		$config_file = $c;
		break;
	}
}
if(!isset($_DEPLOY)){
	$_DEPLOY = \Net\ExternalInterfaces\SSH\Deployment::fromArray($_DEPLOY);
	$live_path = $_DEPLOY->getPath().DIRECTORY_SEPARATOR;
}else{
	\CLI\Output\Error::Fatal('No deployment config provided.');
}
$path = realpath(__DIR__.'/../');

$path = rtrim($path,DIRECTORY_SEPARATOR);
$files = ListDir($path,true);
if(in_array($path.DIRECTORY_SEPARATOR.$config_file,$files)){
//	unset($files[array_search($path.DIRECTORY_SEPARATOR.$config_file, $files)]);
}

foreach($files as $f){
	$rel = substr($f,strlen($path));
	$new_file = $live_path.$rel;
	if(!file_exists($new_file) || (getlastmod($f)>=getlastmod($new_file))){
		\CLI\Output\Error::Notice('Uploading '.$f);
		
		$dirs = array();
		$b = $new_file;
		while(strlen($b = dirname($b)) >= 2){
			$dirs[] = $b;
		}
		$dirs = array_reverse($dirs);
		foreach($dirs as $b){
			@mkdir($b);
		}
		
		if(file_exists($new_file)){
			@unlink($new_file);
		}
		
		@copy($f,$new_file);
		@touch($new_file,getlastmod($f));
	}
}
