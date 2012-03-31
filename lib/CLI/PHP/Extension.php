<?php
namespace CLI\PHP;

class Extension {
	private $name;
	
	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	function __construct($name,$win_name){
		$this->name = $name;
	}
	
	function getLibraryName(){
		return ((PHP_SHLIB_SUFFIX === 'dll') ? 'php_' : '') . $this->name;
	}
	
	function getLibraryFilename(){
		return $this->getLibraryName(). '.' . PHP_SHLIB_SUFFIX;
	}
	
	function isLoaded(){
		return extension_loaded($this->name);
	}
	
	function canLoad(){
		if( !(bool)ini_get( "enable_dl" ) || (bool)ini_get( "safe_mode" ) ) {
			return false;
		}
		return true;
	}
	
	function Load(){
		//Can load and needs to be loaded...
		if(!$this->canLoad() || $this->isLoaded()){
			return false;
		}
		
		//Attempt Load
		return @dl($this->getLibraryFilename());
	}
}