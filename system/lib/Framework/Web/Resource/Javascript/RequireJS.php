<?php
namespace Web\Resource\Javascript;

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
			$config['paths'] = $this->paths;
		}
		
		//Return config array
		return $config;
	}
	private function _buildConfig(){
		$config = $this->_buildConfigArray();
		if($config)	
			return 'requirejs.config('.json_encode($config).');';
	}
	private function _buildInner(){
		return $this->_buildConfig().'require('.json_encode($this->modules).');';
	}
}