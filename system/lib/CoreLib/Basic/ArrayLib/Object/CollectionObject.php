<?php
namespace Basic\ArrayLib\Object;

class CollectionObject implements \IteratorAggregate, \ArrayAccess, \Serializable, \Countable {
	protected $data = array();
	
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
		unset($this->data[$offset]);
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
	
	function _Set($k,$v){
		$this->data[$k] = $v;
	}
	function _Add($k,$v){
		$ret = isset($this->data[$k]);
		$this->_Set($k,$v);
		return $ret;
	}
	function _Get($k){
		if(isset($this->data[$k])){
			return $this->data[$k];
		}
	}
	function Set($k,$v){
		$this->_Set($k,$v);
	}
	function Add($k,$v){
		return $this->_Add($k,$v);
	}
	function Get($k){
		return $this->_Get($k);
	}
	function Remove($k){
		unset($this->data[$k]);
	}
	function asArray(){
		return $this->data;
	}
	function isAssoc () {
		$arr = $this->data;
        return (is_array($arr) && (!count($arr) || count(array_filter(array_keys($arr),'is_string')) == count($arr)));
    }
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