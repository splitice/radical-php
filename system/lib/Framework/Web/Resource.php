<?php
namespace Web;

use Web\Resource\Javascript\Libraries\IJavascriptLibrary;
use Web\Resource\Javascript\RequireJS;
use Utility\HTML\Tag\Script;

class Resource {
	static $javascript = array();
	static $css = array();
	
	public $name;
	public $version;
	public $type;
	
	function __construct($name,$version,$type = 'script'){
		$this->name = $name;
		$this->version = $version;
		$this->type = $type;
	}
	function getModule(){
		$ret = $this->name;
		if($this->version !== null){
			$ret .= '-'.$this->version;
		}
		return $ret;
	}
	function getScript(){
		if($this->type != 'script') throw new \Exception($this->type.' is not a javascript type');
		return new Resource\Javascript\Library($this->name,$this->version);
	}
	
	function getHtml(){
		if($this->type == 'script'){
			return $this->getScript();
		}elseif($this->type == 'css'){
			
		}
		throw new \Exception('Unknown type: '.$this->type);
	}
	
	function getLoadCSS(){
		$library = new Resource\CSS\Library($this->name);
		return 'document.loadCss("'.addslashes($library->attributes['src']).'")';
	}
	
	private static function _type($type){
		if($type == 'script') return 'javascript';
		if($type == 'javascript' || $type == 'css') return $type;
		throw new \Exception('Invalid web resource type of '.$type);
	}
	private static function _create($name,$version,$type){
		return new static($name,$version,$type);
	}
	static function add($name, $version = null, $type = 'script'){
		$type = self::_type($type);
		$a = &self::$$type;
		$a[$name] = self::_create($name,$version,$type);
	}
	static function generate($type = 'script'){
		if($type == 'require.both'){
			$sC = self::generate('require.css');
			$sJ = self::generate('require.js');
			$sC->inner .= $sJ->inner;
			return $sC;
		}elseif($type == 'require.js'){
			$scripts = $paths = array();
			foreach(self::$javascript as $scriptName=>$script){
				$scripts[] = $script->name;
				
				//Is it a CDN hosted library?
				$extLib = Resource\Javascript\Library::Find($script->name,$script->version);
				if($extLib instanceof IJavascriptLibrary){
					$paths[$script->name] = $extLib;
				}
			}
			return new RequireJS($scripts,$paths);
		}elseif($type == 'require.css'){
			$inner = '';
			foreach(self::$css as $styleName=>$css){
				$inner .= $css->getLoadCSS();
			}
			$script = new Script();
			$script->inner = $inner;
			return $script;
		}else{
			$ret = '';
			$type = self::_type($type);
			foreach(self::$type as $v){
				$ret .= $v;
			}
			return $ret;
		}
	}
	static function output($type = 'script'){
		$r = self::generate($type);
		
		if(is_object($r))
			if(!$r->inner) return;
		elseif(!$r) return;
		
		echo $r;
	}
}