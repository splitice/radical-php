<?php
namespace HTTP;

class CachedFetch extends Fetch {
	public $cache;
	
	function __construct($url = null){
		parent::__construct($url);
		$this->cache = '\\HTTP\\Cache\\Mysql';
	}
	
	function Execute($data = null){
		if(isset($this->curl[CURLOPT_POST]) && $this->curl[CURLOPT_POST]){
			return parent::Execute();
		}
		
		
				
		$cache = $this->cache;
		$url = $this->curl[CURLOPT_URL];
		
		$ret = $cache::Get($url);
		if($ret){
			return $ret;
		}
		
		$ret = parent::Execute($data);
		
		$cache::Set($ret,$url);
		return $ret;
	}
}