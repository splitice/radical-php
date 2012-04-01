<?php
namespace Image\Graph;

use Web\Interfaces\IToURL;

class URL implements IToURL{
	protected $module;
	protected $method;
	protected $data = array();
	
	function __construct($module,$method,$data = array()){
		$this->module = $module;
		$this->method = $method;
		$this->data = $data;
	}
	
	function addData($name,$value){
		$this->data[$name] = $value;
	}
	
	function toURL(){
		return 'api/Graph/'.$this->module.'.png?graph='.urlencode($this->method).'&'.http_build_query($this->data);
	}
}