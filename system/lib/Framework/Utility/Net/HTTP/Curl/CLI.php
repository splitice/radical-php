<?php
namespace Utility\Net\HTTP\Curl;

use Utility\Net\HTTP\Curl\CurlBase;
use Utility\System;

class CLI extends CurlBase {
	public $cookieManager;
	public $outputFile;
	
	function __construct($url = null){
		parent::__construct(array(CURLOPT_RETURNTRANSFER => true,CURLOPT_FOLLOWLOCATION=>true));
		if($url){
			$this->setUrl($url);
		}
		
		$this->outputFile = '/tmp/'.md5(time().'|'.microtime(true).'|'.rand(0,10000)).'.curl';
		file_put_contents($this->outputFile,'');
	}
	
	function buildCall(){
		$cmd = 'curl -o '.escapeshellarg($this->outputFile).' ';
		
		foreach($this->data as $k=>$v){
			switch($k){
				case CURLOPT_COOKIE:
					$cmd .= '--cookie='.escapeshellarg($v).' ';
					break;
					
				case CURLOPT_COOKIEJAR:
					$cmd .= '--cookie-jar='.escapeshellarg($v).' ';
					break;
					
				case CURLOPT_FOLLOWLOCATION:
					$cmd .= '--location ';
					break;
					
				case CURLOPT_REFERER:
					$cmd .= '--referer '.escapeshellarg($v).' ';
					break;
					
				case CURLOPT_POST:
					if($v){
						if(isset($this->data[CURLOPT_POSTFIELDS])){
							foreach($this->data[CURLOPT_POSTFIELDS] as $field=>$value){
								/*if(is_string($value) && $value{0}=='@'){
									
								}else{*/
									$cmd .= '--F '.escapeshellarg(urlencode($field).'='.urlencode($value)).' ';
								//}
							}
						}
					}
					break;
			}
		}
		
		$cmd .= '--write-out '.escapeshellarg(self::_writeout_format()).' ';
		
		$cmd .= escapeshellarg($this->data[CURLOPT_URL]);
		
		return $cmd;
	}
	
	const FORMAT_DELIM = '^|^';
	static private function _writeout_format(){
		$vars = array('url_effective','http_code','http_connect','time_total','time_namelookup','time_connect','time_appconnect','time_pretransfer','time_redirect','time_starttransfer','size_download','size_upload','size_header','size_request','speed_download','speed_upload','content_type','num_connects','num_redirects','redirect_url','ftp_entry_path','ssl_verify_result');
		
		$ret = array();
		foreach($vars as $v){
			$ret[] = $v.self::FORMAT_DELIM.'%{'.$v.'}';
		}
		
		return implode('\n',$ret);
	}
	
	function execute($data = null){
		$cmd = $this->buildCall();
		
		$process = new System\Execute($cmd);
		$running = $process->Run();
		$error = $running->ReadAll(System\Process::STDERR);
		$out = $running->ReadAll(System\Process::STDOUT);
		$out = explode("\n",$out);
		
		$info = array();
		foreach($out as $v){
			$v = explode(self::FORMAT_DELIM,$v);
			$info[$v[0]] = $v[1];
		}
		
		if(preg_match('#curl: \([\d]+\)(.+)#',$error,$m)){
			$this->error = trim($m[1]);
			throw new Exception($this->error,$this);
		}
		
		$response = file_get_contents($this->outputFile);
		unlink($this->outputFile);
		
		return new Response($info,$response);
	}
	
	private $error;
	function error(){
		return $this->error;
	}
}