<?php
namespace DDL\Hosts\Check;
use DDL\Hosts\API;

class WUpload extends Internal\HostBase {
	const HOST_SCORE = 0.9;
	const HOST_ABBR = 'WU';
	const HOST_DOMAIN = 'wupload.com';
	const HOST_REGEX = 'wupload\.com/file/([0-9]+)';
	
	function CheckMulti($mh,$url,$callback){
		preg_match('#'.self::HOST_REGEX.'#i', $url, $m);
		
		$obj = new Internal\HostMulti(get_called_class(), $url, $callback);
		
		API\WUpload::LinkCheck($m[1],$mh,array($obj,'Callback'));
	}
	function Check($url){
		preg_match('#'.self::HOST_REGEX.'#i', $url, $m);
		
		$data = API\WUpload::LinkCheck($m[1]);
		
		return self::ValidateCheck($data);
	}
	function ValidateCheck($data){
		$ret = new Internal\CheckReturn($data['status']);
		if(isset($data['filesize']))
			$ret->setFilesize($data['filesize']);
			
		if(isset($data['filename']))
			$ret->setFilename($data['filename']);
		
		return $ret;
	}
	function AppendFilename($url,$filename){
		return $url.'/'.$filename;
	}
}