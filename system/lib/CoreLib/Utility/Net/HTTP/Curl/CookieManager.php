<?php
namespace HTTP\Curl;

class CookieManager {
	private $file;
	public $deleteOnDone = true;
	
	function __construct($file){
		$this->file = $file;
		
		//Create file if it doesnt exist
		if(!file_exists($this->file)){
			file_put_contents($this->file, '');
		}
	}
	function CH($ch){
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->file);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->file);
		return $ch;
	}
	
	function __destruct(){
		if($this->deleteOnDone){
			if(file_exists($this->file)){
				unlink($this->file);
			}
		}
	}
	
	private static $_cache;
	static function Create($file = null){
		//File not set, make it
		if($file === null){
			$file = '/tmp/'.getmypid().'.cookie';
		}
		
		//Setup instance cache if it isnt already
		if(!self::$_cache){
			self::$_cache = new \Cache\Object\WeakRef();
		}
		
		//Check Cache
		$r = self::$_cache->Get($file);
		if($r) return $r;
		
		//Cache miss, Create new and store
		$r = new static($file);
		self::$_cache->Set($file, $r);
		
		//Return new
		return $r;
	}
}