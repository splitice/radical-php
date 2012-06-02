<?php
namespace HTTP\Curl;

use Database\IToSQL;

class Info implements IToSQL {
	public $url;
	public $content_type;
	public $http_code;
	public $header_size;
	public $request_size;
	public $filetime;
	public $ssl_verify_result;
	public $redirect_count;
	public $total_time;
	public $namelookup_time;
	public $connect_time;
	public $pretransfer_time;
	public $size_upload;
	public $size_download;
	public $speed_download;
	public $speed_upload;
	public $download_content_length;
	public $upload_content_length;
	public $starttransfer_time;
	public $redirect_time;
	public $certinfo;
	public $redirect_url;
	
	function __construct($data = array()){
		foreach($data as $k=>$v){
			$this->$k = $v;
		}
		
		//Content-Type
		if($this->content_type){
			$this->content_type = explode(';',$this->content_type);
			foreach($this->content_type as $k=>$v){
				$this->content_type[$k] = trim($this->content_type[$k]);
			}
		}
	}
	
	function toSQL(){
		$ret = array();
		$temp = (array)$this;
		unset($temp['certinfo'],$temp['ssl_verify_result']);
		$temp['content_type'] = implode(';',$temp['content_type']);
		foreach($temp as $k=>$v){
			$ret['info_'.$k] = $v;
		}
		return $ret;
	}
	
	function getContentType(){
		foreach($this->content_type as $v){
			if(strpos($v,'=') === false){
				return $v;
			}
		}
	}
}