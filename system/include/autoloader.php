<?php 
//Class Autoloader
class AutoLoader {
	const BOOTSTRAP_FILE = 'bootstrap.php';
	
	static $instance;
	
	static $baseDir;
	
	static $projectDirs = array('system','app');
	
	function __construct(){
		$this->InitPathCache();
		spl_autoload_register(array($this,'__autoload'));
		static::$instance = $this;
	}
	
	function getPathCache(){
		return $this->pathCache;
	}
	
	private $pathCache = array();
	private function _buildPathCache($libDir){
		$pathCache = array();
		$expr = $libDir.'*';
		foreach(array_reverse(glob($expr)) as $directory){
			if(is_dir($directory)){
				$pathCache[] = rtrim(realpath($directory),DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
			}
		}
		
		//Do ordering
		$bootstrapFile = $libDir.static::BOOTSTRAP_FILE;
		if(file_exists($bootstrapFile)){
			include($bootstrapFile);
				
			if(isset($__BOOTSTRAP__)){//Something is wrong with the file, ignore it
				$newPathCache = array();
				foreach($__BOOTSTRAP__ as $lib){
					foreach($pathCache as $k=>$path){
						$p = basename($path);
						if($lib == $p){
							unset($pathCache[$k]);
							$newPathCache[] = $path;
						}
					}
				}
		
				//Merge ordered results with unordered results
				$newPathCache = array_merge($newPathCache,$pathCache);
		
				//store path cache
				$pathCache = $newPathCache;
			}
		}
		
		return $pathCache;
	}
	function InitPathCache(){
		//Important Paths
		self::$baseDir = realpath(__DIR__ . DIRECTORY_SEPARATOR.'..'. DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR;
		
		//Build Cache
		foreach(self::$projectDirs as $project){
			$libDir = self::$baseDir.DIRECTORY_SEPARATOR.$project.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR;
			
			$this->pathCache = array_merge($this->pathCache,$this->_buildPathCache($libDir));
		}
	}
	
	function resolve($name){
		$name = str_replace ( '\\', '/', $name ).'.php';
		foreach($this->pathCache as $p){
			$path = $p.$name;
			if(file_exists($path)){
				return $path;
			}
		}
		return false;
	}
	
	function __autoload($n){
		if($file = $this->resolve($n)){
			include($file);
			return true;
		}
		return false;
	}
}

new AutoLoader();