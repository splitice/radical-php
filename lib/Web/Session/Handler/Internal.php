<?php
namespace Web\Session\Handler;

use Web\Session\Handler\Internal\ISessionHandler;

class Internal extends Internal\HandlerBase implements ISessionHandler {
	protected $data;
	
	function __construct(){
		session_start();
		$this->data = $_SESSION;
		session_write_close();
		parent::__construct();
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
		session_start();
		unset($_SESSION[$offset]);
		$this->data = $_SESSION;
		session_write_close();
	}
	public function offsetGet($offset) {
		return isset($this->data[$offset]) ? $this->data[$offset] : null;
	}
	
	function get($name){
		return isset($this->data[$offset]) ? $this->data[$offset] : null;
	}
	function set($name,$data){
		session_start();
		if (is_null($name)) {
			$_SESSION[] = $data;
		}else{
			$_SESSION[$name] = $data;
		}
		$this->data = $_SESSION;
		session_write_close();
	}
}