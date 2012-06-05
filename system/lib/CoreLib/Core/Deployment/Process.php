<?php
namespace Core\Deployment;

use Core\Deployment\Remote\IRemoteLocation;

class Process {
	static $COMMON_PATHS = array('css','js','images','web','bin','template','user_include','config.php','favicon.ico','.htaccess','include');
	function getClassesFromCommon(){
		$classes = array();
		foreach(static::$COMMON_PATHS as $p){
			$p = \Core\Libraries::$webDir.$p;
			if(is_dir($p) || pathinfo($p,PATHINFO_EXTENSION) == 'php'){
				foreach(\Folder::ListDir($p,true) as $file){
					if(pathinfo($file,PATHINFO_EXTENSION) == 'php'){
						$c = new \Core\Debug\PHPClassTools($file);
						$d = $c->getDependencies();
						foreach($d as $dd){
							if(\Core\Libraries::toPath($dd,true)){
								$className = ltrim($dd,'\\');
								$classes[$className] = true;
							}
						}
					}
				}
			}
		}
		return array_keys($classes);
	}
	function getClasses(){
		$userLib = $this->getClassesFromCommon();
		foreach(\Core\Libraries::getAllClass() as $class){
			$path =\Core\Libraries::toPath($class,true);
			$path = substr($path,strlen(\Autoloader::$libDir));
			$path = ltrim($path,DIRECTORY_SEPARATOR);
			$path = explode(DIRECTORY_SEPARATOR,$path);
			if($path[0] != 'CoreLib'){
				$userLib[$class] = $class;
			}
		}
		
		$toUpload = array();
		while(count($userLib)){
			$class = array_pop($userLib);
			if(!isset($toUpload[$class])){
				if(oneof($class, '\\Core\\Object')){
					$dependencies = $class::__getDependencies();
				}else{
					if(\Core\Libraries::toPath($class,true)){
						$object = new \Core\ObjectReference($class);
						$dependencies = $object->getDependencies();
					}else{
						$dependencies = array();
					}
				}
		
				foreach($dependencies as $d){
					$dClass = \Core\Provider::Find($d);
					if(!is_array($dClass)){
						$dClass = array($dClass);
					}
					foreach($dClass as $d){
						$userLib[$d] = $d;
					}
				}
				
				//$userLib = array_unique($userLib);
				$toUpload[$class] = true;
			}
		}
		$toUpload = array_keys($toUpload);

		return $toUpload;
	}
	function getFiles(){
		$files = array();
		foreach(static::$COMMON_PATHS as $p){
			$p = \Core\Libraries::$webDir.$p;
			foreach(\Folder::ListDir($p,true) as $file){
				$files[] = $file;
			}
		}
		foreach($this->getClasses() as $class){
			$file = \Core\Libraries::toPath($class,true);
			if($file) $files[] = $file;
		}
		return $files;
	}
	function resolveRelativePath($file){
		$file = ltrim(substr($file,strlen(\Core\Libraries::$webDir)),'\\');
		return $file;
	}
	function Execute(IRemoteLocation $to){
		$files = $this->getFiles();
		foreach($files as $file){
			if($file){
				$to->writeFile($this->resolveRelativePath($file),file_get_contents($file));
			}
		}
	}
}