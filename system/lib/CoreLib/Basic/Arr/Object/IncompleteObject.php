<?php
namespace Basic\ArrayLib\Object;

abstract class IncompleteObject implements \IteratorAggregate, \ArrayAccess, \Serializable, \Countable {
	protected $data = null;
	abstract function getData();
	
	function Init(){
		if($this->data === null){
			$this->data = $this->getData();
		}
	}
	
	/* IteratorAggregate */
	function getIterator() {
		$this->Init();
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
		$this->Init();
		return isset($this->data[$offset]);
	}
	public function offsetUnset($offset) {
		$this->Init();
		unset($this->data[$offset]);
	}
	public function offsetGet($offset) {
		return $this->_Get($offset);
	}
	
	/* Serializable */
	public function serialize() {
		$this->Init();
		return serialize($this->data);
	}
	public function unserialize($data) {
		$this->data = unserialize($data);
	}
	
	/* Countable */
	public function count(){
		$this->Init();
		return count($this->data);
	}
	
	/* IncompleteObject */
	
	function _Set($k,$v){
		$this->Init();
		$this->data[$k] = $v;
	}
	function _Add($k,$v){
		$this->Init();
		$ret = isset($this->data[$k]);
		$this->_Set($k,$v);
		return $ret;
	}
	function _Get($k){
		$this->Init();
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
		$this->Init();
		unset($this->data[$k]);
	}
	function asArray(){
		$this->Init();
		return $this->data;
	}
	function isAssoc () {
		$this->Init();
		$arr = $this->data;
        return (is_array($arr) && (!count($arr) || count(array_filter(array_keys($arr),'is_string')) == count($arr)));
    }
    function GetAll(){
    	$this->Init();
    	return $this->data;
    }
}