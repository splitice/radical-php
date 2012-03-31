<?php 
//Class Autoloader
class AutoLoader {
	const BOOTSTRAP_FILE = 'bootstrap.php';
	
	static $instance;
	
	static $webDir;
	static $libDir;
	static $baseDir;
	
	function __construct(){
		$this->InitPathCache();
		spl_autoload_register(array($this,'__autoload'));
		static::$instance = $this;
	}
	
	function getPathCache(){
		return $this->pathCache;
	}
	
	private $pathCache = array();
	function InitPathCache(){
		//Important Paths
		self::$webDir = realpath(__DIR__ . DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR;
		self::$baseDir = realpath(self::$webDir. DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR;
		self::$libDir = realpath(self::$webDir . 'lib').DIRECTORY_SEPARATOR;
		
		//Build Cache
		foreach(array_reverse(glob(self::$libDir.'*')) as $directory){
			if(is_dir($directory)){
				$this->pathCache[] = rtrim(realpath($directory),DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
			}
		}
		
		//Do ordering
		$bootstrapFile = self::$libDir.DIRECTORY_SEPARATOR.static::BOOTSTRAP_FILE;
		if(file_exists($bootstrapFile)){
			include($bootstrapFile);
			
			if(isset($__BOOTSTRAP__)){//Something is wrong with the file, ignore it
				$newPathCache = array();
				foreach($__BOOTSTRAP__ as $lib){
					foreach($this->pathCache as $k=>$path){
						$p = basename($path);
						if($lib == $p){
							unset($this->pathCache[$k]);
							$newPathCache[] = $path;
						}
					}
				}
				
				//Merge ordered results with unordered results
				$newPathCache = array_merge($newPathCache,$this->pathCache);
				
				//store path cache
				$this->pathCache = $newPathCache;
			}
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