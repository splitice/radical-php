<?php
namespace Web\Session\Storage;

use Web\Session\ModuleBase;

class Database extends ModuleBase implements ISessionStorage {
	protected $id;
	protected $data;
	
	function __construct(){
		session_start();
		$this->data = $_SESSION;
		session_write_close();
		parent::__construct();
	}
	
	function lock_open(){
		throw new \Exception('Not supported');
	}
	
	function lock_close(){
		throw new \Exception('Not supported');
	}
	
	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}

	public function offsetSet($offset, $value) {
		$this->set($offset,$value);
	}
	public function offsetExists($offset) {
		return isset($this->data[$offset]);
	}
	public function offsetUnset($offset) {
		unset($this->data[$offset]);
	}
	public function offsetGet($offset) {
		return isset($this->data[$offset]) ? $this->data[$offset] : null;
	}
	
	function get($name){
		return isset($this->data[$offset]) ? $this->data[$offset] : null;
	}
	function set($name,$data){
		session_start();
		if (is_null($offset)) {
			$_SESSION[$name] = $data;
		}else{
			$_SESSION[] = $data;
		}
		session_write_close();
	}
}