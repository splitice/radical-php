<?php
namespace Net\ExternalInterfaces\ContentAPI\Modules\Internal;
use \Net\ExternalInterfaces\ContentAPI\Interfaces;

abstract class ModuleBase {
	const URL_RULE = '';
	
	protected $id;
	protected $_cache = null;
	
	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}

	static function fromURL($url){
		if(static::RecogniseURL($url)){
			$m = array();
			if(preg_match(static::URL_RULE, $url, $m)){
				return static::fromID($m[1]);
			}
		}
	}
	
	function __construct($id){
		$this->id = $id;
	}
	
	static function CH($url=null){
		$ch = curl_init ( $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt ( $ch, CURLOPT_AUTOREFERER, true );
		return $ch;
	}
	
	static function RecogniseURL($url){
		if(static::URL_RULE){
			return preg_match(static::URL_RULE,$url);
		}
		return false;
	}
	
	function has($str = null){
		if($str){
			return method_exists($this, 'get'.$str);
		}
		$ret = array();
		foreach(get_class_methods($this) as $v){
			if(substr($v,0,3) == 'get'){
				$ret[] = substr($ret,3);
			}
		}
		return $ret;
	}
	
	abstract function Fetch();
	abstract static function getFields();
	
	private function _moduleName(){
		$class = get_called_class();
		$class = array_pop(explode('\\',$class));
		return $class;
	}
	private function _Fetch(){
		$module = $this->_moduleName();
		$server = \Net\ExternalInterfaces\ContentAPI\Config::REMOTE;
		if($server){
			$remote = new \Net\ExternalInterfaces\ContentAPI\Remote($server, $module);
			return $remote->Fetch($this->id);
		}else{
			return $this->Fetch();
		}
	}
	function Parse($want = null,$export = true){
		if($this->_cache !== null){
			if($want){
				$ret = isset($this->_cache[$want])?$this->_cache[$want]:null;
				if($export && is_object($ret) && ($ret instanceof Interfaces\IExportable)){
					$ret = $ret->toExport();
				}
				return $ret;
			}
		}else{
			$cache = \Net\ExternalInterfaces\ContentAPI\Config::getCache();
			if($cache){
				$this->_cache = $cache::Get(get_called_class(),$this->id);
			}
			
			if(!$this->_cache){
				$this->_cache = $this->_Fetch();
				if($this->_cache && $cache){
					$cache::Set($this,$this->_cache);
				}
			}
			
			if($this->_cache){
				if($want) {
					return $this->Parse($want,$export);
				}
			}
		}
		return $this->_cache;
	}
	
	function __call($method,$a){
		return null;//No error!
	}
	
	function toExport(){
		return $this->_cache;
	}
	
	static function HTMLDecode($v) {
		$v = str_replace('&nbsp;', ' ', $v);
		$ret = mb_convert_encoding ( $v, "utf-8", "HTML-ENTITIES" );
	
		return trim($ret);
	}
	
	static function fromID($id){
		return new static($id);
	}
}