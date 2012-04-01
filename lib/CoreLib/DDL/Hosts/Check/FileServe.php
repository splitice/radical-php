<?php
namespace DDL\Hosts\Check;

class FileServe extends Internal\HostBase {
	const HOST_SCORE = 1;
	const HOST_ABBR = 'FSV';
	const HOST_DOMAIN = 'fileserve.com';
	const HOST_REGEX = 'fileserve\.com/file/([0-9A-Za-z]+)';
	
	function ValidateCheck($data) {
		$ret = new Internal\CheckReturn('unknown');
		
		if(strpos($data,'<h1>File not available</h1>')){
			$ret->setStatus('dead');
		}else{
			if(preg_match('#<h1>(.+)<br/></h1>#',$data,$m)){
				$ret->setStatus('ok');
				$ret->setFilename(trim($m[1]));
			}
			if(preg_match('#<strong>(.+)</strong> \| Uploaded#',$data,$m)){
				$fs = \File\Size::fromHuman($m[1]);
				$ret->setFilesize($fs);
			}
		}
		
		return $ret;
	}
	function AppendFilename($url,$filename){
		return $url.'/'.$filename;
	}
}