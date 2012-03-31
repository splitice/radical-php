<?php
namespace Web\PageHandler;

class SubRequestResponse {
	public $data;
	public $headers;
	
	function __construct($data,HeaderManager $headers){
		$this->data = $data;
		$this->headers = $headers;
	}
	
	function __toString(){
		return $this->data;
	}
}