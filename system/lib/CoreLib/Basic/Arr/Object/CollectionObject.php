<?php
namespace Basic\Arr\Object;

use Basic\Arr;

/**
 * The basis for all objects that are also collections.
 * 
 * @author SplitIce
 *
 */
class CollectionObject extends \Core\Object implements \IteratorAggregate, \ArrayAccess, \Serializable, \Countable {
	/**
	 * The array all the data is stored in
	 * 
	 * @var array
	 */
	protected $data = array();//TODO: Make private
	
	/* IteratorAggregate */
	function getIterator() {
		return new \ArrayIterator($this->data);
	}
	
	/* ArrayAccess */
	public function offsetSet($offset, $v) {
		if (is_null($offset)) {
			$this->_Set($this->Count(),$v);
		} else {
			$this->_Set($offset,$v);
		}
	}
	public function offsetExists($offset) {
		return isset($this->data[$offset]);
	}
	public function offsetUnset($offset) {
		$this->Remove($offset);
	}
	public function offsetGet($offset) {
		return $this->_Get($offset);
	}
	
	/* Serializable */
	public function serialize() {
		return serialize($this->data);
	}
	public function unserialize($data) {
		$this->data = unserialize($data);
	}
	
	/* Countable */
	public function count(){
		return count($this->data);
	}
	
	/* CollectionObject */	
	function __construct($data = array()){
		$this->data = $data;
	}
	
	protected function _Set($k,$v){
		if($k === null)
			$this->data[] = $v;
		else
			$this->data[$k] = $v;
	}
	protected function _Add($k,$v){
		$ret = isset($this->data[$k]);
		$this->_Set($k,$v);
		return $ret;
	}
	function _Get($k){
		if(isset($this->data[$k])){
			return $this->data[$k];
		}
	}
	
	/**
	 * Set a value in the array, if it doesnt exist create it.
	 * 
	 * @param mixed $k key
	 * @param mixed $v value
	 */
	function Set($k,$v){
		$this->_Set($k,$v);
	}
	
	/**
	 * Add a value to the array, if it exists overwrite.
	 * 
	 * @param unknown_type $k key
	 * @param unknown_type $v value
	 * @return boolean if it was overwritten
	 */
	function Add($k,$v){
		return $this->_Add($k,$v);
	}
	
	/**
	 * Get a value from an array
	 * 
	 * @param mixed $k key
	 */
	function Get($k){
		return $this->_Get($k);
	}
	
	/**
	 * Remove a value from an array using a key
	 * 
	 * @param mixed $k key
	 */
	function Remove($k){
		unset($this->data[$k]);
	}
	
	/**
	 * Remove from an array where $value matches the value in the array.
	 * 
	 * Or if $value is a callback and $strict is false then where $value(value,key) is true.
	 * 
	 * @param mixed $value a value or callback to compare with
	 * @param bool $strict use strict logic to remove
	 */
	function RemoveWhere($value,$strict = false){
		if(is_callable($value) && $strict === false){
			foreach($this as $k=>$v){
				if($value($v,$k)){
					unset($this[$k]);
				}
			}
		}else{
			foreach($this as $k=>$v){
				if(($strict && $v === $value) || $v == $value){
					unset($this[$k]);
				}
			}
		}
	}
	
	/**
	 * Return the object as a native array.
	 * 
	 * @return array
	 */
	function toArray(){
		return $this->data;
	}

	/**
	 * Return true if the array is associative.
	 * 
	 * @return boolean
	 */
	function isAssoc () {
		return Arr::is_assoc($this->data);
    }
    
    /**
     * Gets values from an array where $value matches the value in the array.
	 * 
	 * Or if $value is a callback and $strict is false then where $value(value) is true.
	 * 
     * @param mixed $callback
     * @param boolean $strict
     * @return array
     */
    function Where($callback,$strict = false){
    	return Arr::where($callback, $this, $strict);
    }
    
    /**
     * Empty the array
     */
    function Clear(){
    	$this->data = array();
    }
    
    /* TODO: Find better names */
    function GetAll(){
    	return $this->data;
    }
    function SetAll($array){
    	$this->data = $array;
    	return $this;
    }
    
    /**
     * Applies a callback to a copy of all data in the collection
     * and returns the result.
     *
     * @param callback $filter The filter to apply.
     * @param bool $return The result will be returned rather than stored in the array
     * @return mixed The filtered items. Will be an array unless $return is true in the,
     *  then an instance of this class will be returned.
     */
    public function Map($filter, $return = false) {
    	$data = array_map($filter, $this->data);
    
    	if ($return) {
    		$class = get_called_class();
    		return new $class($this->data);
    	}
    	$this->data = $data;
    	return $this;
    }
}