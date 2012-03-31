<?php
namespace Database\Model\DynamicTyping;

use Debug\Inspector;

use Database\Model\TableReferenceInstance;

class Instance {
	public $map = array();
	private $cache;
	
	function __construct(TableReferenceInstance $table){
		$class = $table->getClass();
		$this->cache = \Cache\PooledCache::Get(__CLASS__, 'Memory');
		$this->map = $this->getMap($class);
	}
	private function getMap($class){
		$file = \Libraries::path($class);
		$cacheKey = $class.'_'.filemtime($file);
		$co = $this->cache->Get($cacheKey);
		if($co !== null) {
			if(!is_array($co)){
				die(var_dump($co));
			}
			return $co;
		}
		$co = $this->_getMap($class);
		$this->cache->Set($cacheKey,$co);
		return $co;
	}
	private function _getMap($class){
		$properties = Inspector::properties($class,array('public'=>false));
		
		//parse out fields
		$fields = array();
		foreach($properties as $p){
			if(in_array('protected',$p['modifiers'])){
				$name = $p['name'];
				if($name{0} != '_'){
					$fields[$name] = Docblock::comment($p['docComment']);
				}
			}
		}
		
		//Parse out types
		$ret = array();
		foreach($fields as $field => $data){
			if(isset($data['tags']['var'])){
				$ret[$field] = $this->dynamicType($data['tags']['var']);
			}
		}
		
		return $ret;
	}
	private function dynamicType($var){
		$var = explode(' ',$var);
		$extra = array_slice($var,1);
		$var = $var[0];
		
		return compact('var','extra');
	}
}