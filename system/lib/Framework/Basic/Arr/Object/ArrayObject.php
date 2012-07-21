<?php
namespace Basic\Arr\Object;

/**
 * Array object. The basis for objects that are also indexed arrays
 * 
 * @author SplitIce
 *
 */
class ArrayObject extends CollectionObject {
	/* (non-PHPdoc)
	 * @see \Basic\Arr\Object\CollectionObject::Set()
	 */
	function Set($v){
		return parent::Set(null, $v);
	}
	
	/* (non-PHPdoc)
	 * @see \Basic\Arr\Object\CollectionObject::Add()
	 */
	function Add($v){
		return parent::Add(null, $v);
	}
	
	/* (non-PHPdoc)
	 * @see \Basic\Arr\Object\CollectionObject::Remove()
	 */
	function Remove($k){
		parent::Remove($k);
		
		//Reorder over gap
		$this->data = array_values($this->data);
	}
	
	function UnShift($value){
		array_unshift ($this->data,$value);
	}
	
	function Pop(){
		return array_pop($this->data);
	}
}