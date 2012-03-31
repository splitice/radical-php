<?php
namespace Web\PageHandler;

class MetaManager extends \Basic\ArrayLib\Object\CollectionObject {
	function __construct($data = array()){
		if(!isset($data['keywords'])){
			$data['keywords'] = array();
		}
		parent::__construct($data);
	}
	function Get($k){
		$r = parent::Get($k);
		if(is_array($r)) $r = implode(',',$r);
		return $r;
	}
	function AddA($k,$v, $unique = true){
		$a = $this->Get($k);
		if(is_array($a)){
			$a[] = $v;
			$a = array_unique($a);
		}
		$this->Set($k,$a);
	}
	function toTag($k){
		return '<meta name="'.$k.'" content="'.$this->Get($k).'" />';
	}
}