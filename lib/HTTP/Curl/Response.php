<?php
namespace HTTP\Curl;

class Response {
	private $response;
	public $info;
	
	function __construct($ch,$data){
		$this->response = $data;
		if(is_resource($ch)){
			$data = curl_getinfo($ch);
		}elseif(is_array($ch)){
			$data = $ch;
		}else{
			throw new \Exception('Invalid format for $ch. Not a curl handle, not an array');
		}
		
		$this->info = new Info($data);
	}
	
	function getInfo(){
		return $this->info;
	}
	
	function getCode(){
		return $this->info->http_code;
	}
	
	function getResponse(){
		return $this->response;
	}
	
	function __toString(){
		return $this->response;
	}
	
	function toSQL(){
		$ret = $this->info->toSQL();
		$ret['response'] = $this->response;
		return $ret;
	}
}