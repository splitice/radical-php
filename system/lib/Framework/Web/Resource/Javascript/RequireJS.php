<?php
namespace Web\Resource\Javascript;

use Web\Resource\Javascript\Libraries\IJavascriptLibrary;

use Utility\HTML\Tag\Script;

class RequireJS extends Script {
	protected $modules = array();
	protected $paths = array();
	
	function __construct($modules = null,$paths = array()){
		$this->paths = $paths;
		if($modules != null) $this->addModule($modules);
	}
	
	function addModule($module){		
		//Multiples passed
		if(is_array($module)){
			foreach($module as $m){
				$this->addModule($m);
			}
			return;
		}
		
		//Do form module
		$this->modules[] = $module;
		$this->inner = $this->_buildInner();
	}
	protected function _buildConfigArray(){
		$config = array();
		
		//If we have defined paths add to config;
		if($this->paths){
			$path = array();
			foreach($this->paths as $k=>$e){
				if($e instanceof IJavascriptLibrary){
					$path[$k] = substr((string)$e,0,-3);
				}
			}
			$config['paths'] = $path;
		}
		
		//Return config array
		return $config;
	}
	private function _buildConfig(){
		$config = $this->_buildConfigArray();
		if($config)	
			return 'requirejs.config('.json_encode($config).');';
	}
	function findDepth($tree,&$deps,$depth = 1){
		$updated = false;
		foreach($deps as $k=>$v){
			if(false !== array_search($v, $tree)){
				unset($deps[$k]);
				$updated = true;
			}
		}

		if($deps){
			foreach($tree as $v){
				if(is_array($v)){
					$depth = $this->findDepth($tree, &$deps, $depth+1);
					if($depth){
						return $depth;
					}
				}
			}
		}
		
		return $updated?$depth:$updated;
	}
	private function _buildLoadTree(){
		$tree = array();
		foreach($this->modules as $m){
			if(isset($this->paths[$m])){
				$pm = $this->paths[$m];
				if($pm->depends){
					$deps = $pm->depends;
					$depth = $this->findDepth($tree, $deps);

					$p = null;
					$s = &$tree;
					for($i=0;$i<$depth;$i++){
						$p = &$s;
						foreach($s as $k=>$v){
							if(is_array($v)){
								$s = &$s[$k];
								$p = null;
								break;
							}
						}
					}
					
					if($p === null){
						$s[] = $m;
					}else{
						$p[] = array($m);
					}
				}else{
					$tree[] = $m;
				}
			}else{
				$tree[] = $m;
			}
		}
		return $tree;
	}
	private function buildRequire($tree){
		$callback = false;
		$temp = array();
		foreach($tree as $v){
			if(is_array($v)){
				$callback = $this->buildRequire($v);
			}else{
				if($v == 'jQuery') $v = 'jquery';//Special jquery stuff
				$temp[] = $v;
			}
		}
		
		$require = 'require('.json_encode($temp);
		if($callback) $require .= ','.$callback;
		$require .= ')';
		
		return $require;
	}
	private function _buildInner(){
		$tree = $this->_buildLoadTree();
		$require = $this->buildRequire($tree);
		
		return $this->_buildConfig().$require;
	}
}