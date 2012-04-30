<?php
namespace DDL\Hosts\Check;

class MegaUpload extends Internal\HostBase {
	const HOST_SCORE = 1.4;
	const HOST_ABBR = 'MU';
	const HOST_DOMAIN = 'megaupload.com';
	const HOST_REGEX = 'megaupload\.com/\?d=([a-zA-Z0-9]+)';
	
	function ValidateCheck($data) {
		$ret = new Internal\CheckReturn('unknown');
		
		if(strpos($data,'Unfortunately, the link you have clicked is not available.')){
			$ret->setStatus('dead');
		}else{
			if(preg_match('#<div class="download_file_name">([^>]+)</div>#',$data,$m)){
				$ret->setStatus('ok');
				$ret->setFilename(trim($m[1]));
			}
			if(preg_match('#<div class="download_file_size">([^>]+)</div>#',$data,$m)){
				$fs = \File\Size::fromHuman(trim($m[1]));
				$ret->setFilesize($fs);
			}
		}
		
		return $ret;
	}
}