<?php
namespace CLI\Threading;

/* One thread has one of these so they can be cleaned up */
class Object {
	private $_data = array();
	private $_pre_cache = array();
	
	function Register($object){
		if(is_a($object,'CLI\\Threading\\IForkAction')){
			$this->_data[] = $object;
			return true;
		}else{
			return false;
		}
	}
	function preFork(){
		foreach($this->_data as $k=>$o){
			$this->_pre_cache[$k] = $this->call($o,'preFork');
		}
	}
	function postFork(){
		foreach($this->_data as $k=>$o){
			$this->call($o,'postFork',array($this->_pre_cache[$k]));
		}
	}
	private function call($object,$method,$arguments = array()){
		return call_user_func_array(array($object,$method),$arguments);
	}
}