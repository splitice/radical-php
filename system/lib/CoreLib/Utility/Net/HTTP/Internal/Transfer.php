<?php
namespace HTTP\Internal;

class Transfer extends FetchBase {
	private $id;
	private $curl;
	private $callback;
	private $parent;
	private $time;
	private $obj;
	
	function __construct(\HTTP\Fetch $obj,\HTTP\Multi $parent,$callback=null,$id=null){
		$this->obj = $obj;
		$this->callback = $callback;
		$this->parent = $parent;
		$this->id = $id;
		$this->time = time();
		$this->curl = $obj->CH();
	}
	
	function Expired(){
		$timeout_when = $this->time+$this->obj->getTimeout();
		if($timeout_when<time()){
			return true;
		}
		return false;
	}
	
	/**
	 * @return the $curl
	 */
	public function getCurl() {/* CH */
		return $this->curl;
	}

	/**
	 * @return the $obj
	 */
	public function getObj() {
		return $this->obj;
	}

	function getId(){
		return $this->id;
	}
	
	private $_data;
	function getData(){
		if($this->_data) return $this->_data;
		
		if($this->isError) throw new \Exception('Cant get data from an error');
		
		$data = curl_multi_getcontent($this->curl);
		$this->_data = $this->obj->Execute($data);
		return $this->_data;
	}
	
	/**
	 * @return the $parent
	 */
	public function getParent() {
		return $this->parent;
	}

	private $isError = false;
	function Call($err=null){	
		if($err){
			$this->isError = true;	
		}
		$ret = call_user_func($this->callback,$this,$err);
		
		if($ret instanceof Transfer){
			$this->parent->addTransfer($ret);
		}
	}
	function onError($string){
		$this->Call($string);
	}
	function __destruct(){
		unset($this->parent);
	}
}