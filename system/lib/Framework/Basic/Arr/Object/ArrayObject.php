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
	function set($v){
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
	function remove($k){
		parent::Remove($k);
		
		//Reorder over gap
		$this->data = array_values($this->data);
	}
	
	function unShift($value){
		array_unshift ($this->data,$value);
	}
	
	function pop(){
		return array_pop($this->data);
	}
}