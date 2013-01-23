<?php
namespace Web\Page\Handler;

class MetaManager extends \Basic\Arr\Object\CollectionObject {
	function __construct($data = array()){
		if(!isset($data['keywords'])){
			$data['keywords'] = array();
		}
		$data['charset'] = 'utf-8';
		parent::__construct($data);
	}
	function add_array($k,$v, $unique = true){
		$a = $this->Get($k);
		if(is_array($a)){
			$a[] = $v;
			$a = array_unique($a);
		}
		$this->Set($k,$a);
	}
	function add_keyword($v, $unique = true){
		$this->add_array('keywords', $v, $unique);
	}
	function toTag($k,$v = null){
		if($v === null)
			$v = $this->get($k);
		if($v){
			if(is_array($v)) $v = implode(',',$v);
			
			return '<meta name="'.$k.'" content="'.$v.'" />'."\r\n";
		}
	}
}