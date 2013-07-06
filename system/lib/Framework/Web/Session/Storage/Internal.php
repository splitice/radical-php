<?php
namespace Web\Session\Storage;

use Web\Session\ModuleBase;
use Core\Server;

class Internal extends ModuleBase implements ISessionStorage {
	protected $data;
	private $is_open = false;
	private $is_empty = false;
	private $is_loaded = false;
	private $is_cli;
	
	function __construct(){
		$this->is_cli = Server::isCLI();
		parent::__construct();
	}
	
	private function _open(){
		if($this->is_open)
			return false;
		
		if(!$this->is_cli){
			session_start();
			if(count($_SESSION) == 0)
				$this->is_empty = true;
		}
		
		$this->is_open = true;
		return true;
	}
	private function _close(){
		if(!$this->is_open)
			return false;
		
		if(!$this->is_cli){
			if(count($_SESSION) == 0  && $this->is_empty){
				session_destroy();
			}else{
				session_write_close();
			}
		}
		
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
		$this->is_loaded = true;
		$open = $this->is_open;
		if(!$open)
			$this->_open();
		
		if(!$this->is_cli)
			$this->data = $_SESSION;
		
		if(!$open)
			$this->_close();
	}
	
	function getId(){
		return session_id();
	}
	
	public function offsetSet($offset, $value) {
		if(!$this->is_loaded)
			$this->refresh();
		$this->set($offset,$value);
	}
	public function offsetExists($offset) {
		if(!$this->is_loaded)
			$this->refresh();
		return isset($this->data[$offset]);
	}
	public function offsetUnset($offset) {
		if(!$this->is_loaded)
			$this->refresh();
		$open = $this->is_open;
		if(!$open)
			$this->_open();
		unset($_SESSION[$offset]);
		$this->data = $_SESSION;
		if(!$open)
			$this->_close();
	}
	public function offsetGet($offset) {
		if(!$this->is_loaded)
			$this->refresh();
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