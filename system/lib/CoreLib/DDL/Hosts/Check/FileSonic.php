<?php
namespace DDL\Hosts\Check;
use DDL\Hosts\API;
use DDL\Hosts\Internal\CheckReturn;

class FileSonic extends Internal\HostBase {
	const HOST_SCORE = 1;
	const HOST_ABBR = 'FSC';
	const HOST_DOMAIN = 'filesonic.com';
	const HOST_REGEX = 'filesonic\.([a-z]+)/file/([0-9A-Za-z]+)';

	function CheckMulti($mh,$url,$callback){
		preg_match('#'.self::HOST_REGEX.'#i', $url, $m);
		
		$obj = new Internal\HostMulti(get_called_class(), $url, $callback);
		
		API\FileSonic::LinkCheck($m[2],$mh,array($obj,'Callback'));
	}
	function Check($url){
		preg_match('#'.self::HOST_REGEX.'#i', $url, $m);
		
		$data = API\FileSonic::LinkCheck($m[2]);
		
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