<?php
namespace HTTP;

use HTTP\Curl\CurlBase;

class Curl extends CurlBase {
	private $ch;
	public $cookieManager;
	
	function __construct($url = null){
		$this->ch = curl_init();
		parent::__construct(array(CURLOPT_RETURNTRANSFER => true,CURLOPT_FOLLOWLOCATION=>true));
		if($url){
			$this->setUrl($url);
		}
	}
	
	function CH(){
		$ret = curl_setopt_array($this->ch, $this->data);
		if(!$ret){
			throw new \Exception('Could not set all curl options');
		}
		if($this->cookieManager){
			$this->cookieManager->CH($this->ch);
		}else{
			$this->data[CURLOPT_COOKIEJAR] = null;
			$this->data[CURLOPT_COOKIEFILE] = null;
		}
		return $this->ch;
	}
	
	function Execute($data = null){
		$ch = $this->CH();
		if($data === null){
			$ret = curl_exec($ch);
		}else{
			$ret = $data;
		}
		
		if($ret === false){
			throw new Curl\Exception($this->Error(),$this);
		}
		return new Curl\Response($this->ch, $ret);
	}
	
	function Error(){
		return curl_error($this->ch);
	}
}