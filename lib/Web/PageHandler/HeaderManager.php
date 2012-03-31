<?php
namespace Web\PageHandler;

class HeaderManager {
	private $headers = array('Cache-Control'=>'no-cache');
	private $status_code = 200;
	
	const DEFAULT_EXPIRE = 1200;//20minutes
	
	function __construct($headers=null){
		if($headers!==null){
			$this->headers = $headers;
		}
		$this->setExpires(time()+self::DEFAULT_EXPIRE);
		
	}
	function Clear(){
		$this->headers = array();
	}
	function Add($k,$v){
		$this->headers[$k] = $v;	
	}
	function Status($code){
		$this->status_code = $code;
	}
	function getHeaders(){
		return $this->headers;
	}
	function setCache($time){
		if(is_numeric($time)){
			$this->Add('Cache-Control','max-age='.$time);
		}else{
			$this->Add('Cache-Control',$time);
		}
	}
	function setEtag($tag){
		$this->Add('ETag','"'.$tag.'"');
	}
	function setLastModified($time){
		$this->Add('Last-Modified',gmdate('D, d M Y H:i:s', $time) . ' GMT');
	}
	function setExpires($time){
		$this->Add('Expires',gmdate('D, d M Y H:i:s', $time) . ' GMT');
	}
	function setContentLength($bytes){
		$this->Add('Content-Length',$bytes);
	}
	function Output(){
		if(!$this->headers){
			header($this->status_code.' A', true, $this->status_code);
		}
		foreach($this->headers as $k=>$v){
			header($k.': '.$v,true,$this->status_code);
		}
	}
}