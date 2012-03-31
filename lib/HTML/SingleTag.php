<?php
namespace HTML;

class SingleTag extends \Core\Object {
	protected $tag;
	public $attributes = array();
	
	function __construct($tag,$attributes = array()){
		$this->tag = $tag;
		$this->attributes = $attributes;
	}
	
	function __toString(){
		$ret = '<'.$this->tag;
		foreach($this->attributes as $k=>$v){
			if($v !== null){
				$ret .= ' '.$k.'="'.addslashes($v).'"';
			}
		}
		$ret .= '>';
		return $ret;
	}
}