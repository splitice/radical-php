<?php
namespace Model\Database\ORM\DynamicTyping;

use Core\Debug\Inspector;
use Model\Database\Model\TableReferenceInstance;

class Instance {
	public $map = array();
	private $cache;
	
	function __construct(TableReferenceInstance $table){
		$class = $table->getClass();
		$this->map = $this->getMap($class);
	}
	private function getMap($class){
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
		
		//Prefix if not given
		if((strpos($var, '\\') === false) || ($var{0} != '\\' && !class_exists($var))){
			$var = '\\Model\\Database\\DynamicTypes\\'.$var;
		}
		
		return compact('var','extra');
	}
}