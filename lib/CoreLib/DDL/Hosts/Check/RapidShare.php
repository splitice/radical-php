<?php
namespace DDL\Hosts\Check;

class RapidShare extends Internal\HostBase {
	const HOST_SCORE = 1.5;
	const HOST_ABBR = 'RS';
	const HOST_DOMAIN = 'rapidshare.com';
	const HOST_REGEX = 'rapidshare\.com/files/([0-9]+)/([a-zA-Z0-9_\-\.]+)';
	
	function Check($url){
		preg_match('#'.self::HOST_REGEX.'#i', $url, $m);
		$F = new \HTTP\Fetch('http://api.rapidshare.com/cgi-bin/rsapi.cgi?sub=checkfiles&files='.urlencode($m[1]).'&filenames='.urlencode($m[2]));
		$F->setModule('Link Checker');
		$result = $F->Execute();
		
		if($result->hasContent()){
			$data = $result->getContent();
			return self::ValidateCheck($data);
		}
		
		return new Internal\CheckReturn('unknown');
	}
	function CheckMulti($mh,$url,$callback){
		preg_match('#'.self::HOST_REGEX.'#i', $url, $m);
		$F = new \HTTP\Fetch('http://api.rapidshare.com/cgi-bin/rsapi.cgi?sub=checkfiles&files='.urlencode($m[1]).'&filenames='.urlencode($m[2]));
		
		$F->setTimeout(5);
		$F->setModule('Link Checker');
		
		$obj = new Internal\HostMulti(get_called_class(), $url, $callback);
		
		$mh->Add($F,array($obj,'Callback'));
	}
	function ValidateCheck($data) {
		$ret = new Internal\CheckReturn('unknown');
		
		$content = explode(',',trim($data));
		//$ret->setFilename(trim($m[1]));
		if($content[4]==1 || $content[4]>=50){
			$ret->setStatus('ok');
		}else{
			$ret->setStatus('dead');
		}
		if($content[1]!='-')
			$ret->setFilename($content[1]);
		if($content[2]!='-')
			$ret->setFilesize($content[2]);
			
		return $ret;
	}
}