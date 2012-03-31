<?php
namespace Basic\ArrayLib\Object;

class ArrayObject extends CollectionObject {
	function Set($v){
		return parent::Set($this->Count(), $v);
	}
	function Add($v){
		return parent::Add($this->Count(), $v);
	}
	function Remove($k){
		parent::Remove($k);
		$this->data = array_values($this->data);
	}
}