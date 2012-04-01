<?php
namespace Cache\Object;

class WeakRef {
	private $data = array();
	private $weakrefSupport;
	
	function __construct($support = null){
		if($support === null){
			$this->weakrefSupport = class_exists('Weakref');
		}else{
			$this->weakrefSupport = $support;
		}
	}
	function Get($key){
		if(is_object($key)){
			$key = (string)$key;
		}
		if(!isset($this->data[$key])) return null;
		$ret = $this->data[$key];
		if($this->weakrefSupport){
			if($ret->valid()){
				$ret = $ret->get();
			}else{
				unset($this->data[$key]);
				$ret = null;
			}
		}
		return $ret;
	}
	function Set($key,$value,$ttl = null){
		if($this->weakrefSupport){
			$value = new \WeakRef($value);
		}
		if(is_object($key)){
			$key = (string)$key;
		}
		$this->data[$key] = $value;
	}
	function Delete($key){
		if(isset($this->data[$key])){
			unset($this->data[$key]);
		}
	}
}