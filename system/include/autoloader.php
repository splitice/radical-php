<?php 
//Class Autoloader
class AutoLoader {
	const BOOTSTRAP_FILE = 'bootstrap.php';
	static $projectDirs = array('app','system');
	private $cache = array();
	static $pathCache = array();
	
	function __construct(){
		static::InitPathCache();
		
		global $BASEPATH;
		$al_key = 'xal_'.filemtime(__FILE__).md5($BASEPATH);
		$this->cache = apc_fetch($al_key);
		if(!$this->cache){
			$this->cache = array();
			global $BASEPATH;
			foreach(array_reverse(self::$pathCache) as $path){
				$items = glob ( $path . '/*' );
					
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
				
				foreach($items as $dir){
					$path = substr($dir, strlen($BASEPATH));
					$dir = str_replace('/','\\',substr(strstr($path, '/lib/'),5,-4));
					$dir = substr(strstr($dir, '\\'),1);
					$this->cache[$dir] = $path;
				}
			}
			
			apc_store($al_key, $this->cache, 300);
		}
		
		spl_autoload_register(array($this,'__autoload'));
	}
	
	private static function _buildPathCache($libDir){
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
	static function initPathCache(){
		global $BASEPATH;
		
		//Build Cache
		foreach(self::$projectDirs as $project){
			$libDir = $BASEPATH.DIRECTORY_SEPARATOR.$project.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR;
			
			static::$pathCache = array_merge(static::$pathCache,static::_buildPathCache($libDir));
		}
	}
	
	static function resolve($name){
		$name = str_replace ( '\\', '/', $name ).'.php';
		foreach(static::$pathCache as $p){
			$path = $p.$name;
			if(file_exists($path)){
				return $path;
			}
		}
		return false;
	}
	
	function __autoload($n){
		if(isset($this->cache[$n])){
			global $BASEPATH;
			$file = $BASEPATH.$this->cache[$n];
		}else{
			if($n{0} == '_' && $n{1} == '\\'){
				$file = \Core\MagicNamespace::fromString($n)->Load();
			}else{
				$file = static::resolve($n);
			}
		}
		if($file){
			include($file);
			return true;
		}
		return false;
	}
}

new AutoLoader();