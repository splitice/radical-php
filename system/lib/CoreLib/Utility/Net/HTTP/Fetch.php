<?php
namespace Utility\Net\HTTP;

class Fetch {	
	public $curl;
	private $headers = array();
	
	function __construct($url=false,$class='\\Utility\\Net\\HTTP\\Curl'){
		$this->curl = new $class($url);
		
		global $_CONFIG;
		if(isset($_CONFIG['HTTP']['UA'])){
			$this->setUserAgent($_CONFIG['HTTP']['UA']);
		}
		$this->setTimeout(300);
		$this->setConnectTimeout(10);
		
		$this->curl->cookieManager = Curl\CookieManager::Create();
	}
	function setUrl($url){
		$this->curl[CURLOPT_URL] = $url;
		return $this;
	}
	function setUserAgent($ua){
		$this->curl[CURLOPT_USERAGENT] = $ua;
		return $this;
	}
	function setProxy($ip_addr,$port=80,$proxy_details=array(),$httptunnel=false,$proxy_type=CURLPROXY_HTTP){
            
                //set ip adress, can be a domain name
		$this->curl[CURLOPT_PROXY] = $$ip_addr;
                
                //set proxy port
                if(is_int($port))
                        $this->curl[CURLOPT_PROXYPORT] = $port;
                
                //set proxy auth
                if((is_array($proxy_details)) &&(sizeof($proxy_details) == 2))
                        $this->curl[CURLOPT_PROXYUSERPWD] = implode(':',$proxy_details);
                
                //set proxy tunnel mode
                if($httptunnel)
                         $this->curl[CURLOPT_HTTPPROXYTUNNEL] = true;
                
                //set proxy type
                $this->curl[CURLOPT_PROXYTYPE] = $proxy_type;

	}
        function setInterface($interface){
                $this->curl[CURLOPT_INTERFACE] = $interface;
        }
        //TODO; setRanges($array) array having X and Y, or  having arrays of Xs and Ys
	function setHeader($name,$value){
		$this->headers[$name] = $value;
		$r = array();
		foreach($this->headers as $k=>$v){
			$r[] = $k.': '.$v;
		}
		$this->curl[CURLOPT_HTTPHEADER] = $r;
	}
	function setReferer($url){
		if(is_bool($url)){
			$this->curl[CURLOPT_FOLLOWLOCATION] = $url;
		}else{
			$this->curl[CURLOPT_REFERER] = $url;
		}
		return $this;
	}
	function setTimeout($time){
		$this->curl[CURLOPT_TIMEOUT] = $time;
		return $this;
	}
	function setConnectTimeout($time){
		$this->curl[CURLOPT_CONNECTTIMEOUT] = $time;
		return $this;
	}
	function getTimeout(){
		if(isset($this->curl[CURLOPT_TIMEOUT])){
			return $this->curl[CURLOPT_TIMEOUT];
		}
	}
	
	function Post($data){
		//Store previous post state
		$post = $data = null;
		if(isset($this->curl[CURLOPT_POST])){
			$post = $this->curl[CURLOPT_POST];
		}
		if(isset($this->curl[CURLOPT_POSTFIELDS])){
			$data = $this->curl[CURLOPT_POSTFIELDS];
		}
		
		//Setup Post
		$this->curl[CURLOPT_POST] = true;
		$this->curl[CURLOPT_POSTFIELDS] = $data;
		
		//Execute
		$ret = $this->Execute();
		
		//Reset
		if($post === null){
			unset($this->curl[CURLOPT_POST]);
		}else{
			$this->curl[CURLOPT_POST] = $post;
		}
		if($data === null){
			unset($this->curl[CURLOPT_POSTFIELDS]);
		}else{
			$this->curl[CURLOPT_POSTFIELDS] = $data;
		}
		
		return $ret;
	}
	
	function Get(){
		//Store previous post state
		$post = $data = null;
		if(isset($this->curl[CURLOPT_POST])){
			$post = $this->curl[CURLOPT_POST];
		}
		if(isset($this->curl[CURLOPT_POSTFIELDS])){
			$data = $this->curl[CURLOPT_POSTFIELDS];
		}
		
		//Setup Get
		$this->curl[CURLOPT_POST] = false;
		$this->curl[CURLOPT_POSTFIELDS] = array();
		
		//Execute
		$ret = $this->Execute();
		
		//Reset
		if($post === null){
			unset($this->curl[CURLOPT_POST]);
		}else{
			$this->curl[CURLOPT_POST] = $post;
		}
		if($data === null){
			unset($this->curl[CURLOPT_POSTFIELDS]);
		}else{
			$this->curl[CURLOPT_POSTFIELDS] = $data;
		}
		
		//Return
		return $ret;
	}
	
	function CH(){
		$ch = $this->curl->CH();
		return $ch;
	}
	
	/*if($this->pre_resolve){
		$host = parse_url($url,PHP_URL_HOST);
		$url = parse_url($url,PHP_URL_SCHEME).'://'.$this->pre_resolve;
		$port = parse_url($url,PHP_URL_PORT);
		if($port){
			$url .= ':'.$port;
		}
		$url .= parse_url($url,PHP_URL_PATH).parse_url($url,PHP_URL_QUERY);
	}*/
	/*if($this->pre_resolve){
		$headers[] = 'Host: '.$host;
	}
	if($LIGHTHTTPD_TEST_MODE === true){//lighttpd bug workaround
		$headers = array("Expect:");
	}
	$headers[] = 'Accept-Language: en-US,en;q=0.8,en-GB;q=0.6';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);*/

	function Execute($data = null){
		return $this->curl->Execute($data);
	}
}