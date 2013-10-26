<?php
namespace Web\Page\Handler;

use Basic\Arr\Object\CollectionObject;

class HeaderManager extends CollectionObject {
	const DEFAULT_EXPIRE = 1200;//20minutes
	
	public $status = 200;
	
	function __construct($headers=null){
		if($headers!==null){
			parent::__construct($headers);
		}else{
			parent::__construct(array('Cache-Control'=>'no-cache'));
		}
		$this->setExpires(time()+self::DEFAULT_EXPIRE);
		
	}
	function status($code){
		$this->status = $code;
	}
	function getHeaders(){
		return $this->data;
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
	function setContentType($mime){
		$this->Add('Content-Type',$mime);
	}
	function output(){
		
		//No data
		if(!$this->data){
			header($this->status.' A', true, $this->status);
		}else{		
			foreach($this->data as $k=>$v){
				header($k.': '.$v,true, $this->status);
			}
		}
	}
}