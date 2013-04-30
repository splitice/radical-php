<?php
namespace Web\Session\Storage;

use Web\Session\ModuleBase;

class Internal extends ModuleBase implements ISessionStorage {
	protected $data;
	private $is_open = false;
	
	function __construct(){
		$this->refresh();
		parent::__construct();
	}
	
	private function _open(){
		if($this->is_open)
			return false;
		
		session_start();
		$this->is_open = true;
		return true;
	}
	private function _close(){
		if(!$this->is_open)
			return false;
		
		session_write_close();
		$this->is_open = false;
		return true;
	}
	
	function lock_open(){
		return $this->_open();
	}
	
	function lock_close(){
		return $this->_close();
	}
	
	function refresh(){
		$open = $this->is_open;
		if(!$open)
			$this->_open();
		$this->data = $_SESSION;
		if(!$open)
			$this->_close();
	}
	
	function getId(){
		return session_id();
	}
	
	public function offsetSet($offset, $value) {
		$this->set($offset,$value);
	}
	public function offsetExists($offset) {
		return isset($this->data[$offset]);
	}
	public function offsetUnset($offset) {
		$open = $this->is_open;
		if(!$open)
			$this->_open();
		unset($_SESSION[$offset]);
		$this->data = $_SESSION;
		if(!$open)
			$this->_close();
	}
	public function offsetGet($offset) {
		return isset($this->data[$offset]) ? $this->data[$offset] : null;
	}
	
	function get($name){
		return isset($this->data[$name]) ? $this->data[$name] : null;
	}
	function set($name,$data){
		$open = $this->is_open;
		if(!$open)
			$this->_open();
		if (is_null($name)) {
			$_SESSION[] = $data;
		}else{
			$_SESSION[$name] = $data;
		}
		$this->data = $_SESSION;
		if(!$open)
			$this->_close();
	}
}