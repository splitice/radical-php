<?php
namespace Net\ExternalInterfaces\Nginx;
class ProxyConfig {
	var $ip_addr;
	var $to_addr;
	var $ports = array ();
	var $SSL = false;
	var $SSL_KEY;
	var $SSL_CERT;
	var $SSL_ROUTE = 0;
	var $Cache = false;
	var $keepalive = false;
	
	const ROUTE_PORT80 = 0;
	const ROUTE_NONE = 1;
	
	private $upstream_count = 0;
	
	static function IP(\Net\IP $ip) {
		if ($ip->getVersion() == 6) {
			return '[' . $ip . ']';
		}
		return $ip;
	}
	
	function __construct(\Net\IP $ip_addr, \Net\IP $to_addr) {
		if ($ip_addr->isValid()) {
			$this->ip_addr = $ip_addr;
		}
		
		if ($to_addr->isValid()) {
			$this->to_addr = $to_addr;
		}
	}
	function addPort($port) {
		if($port < 50){
			return false;
		}		
		
		$this->ports [] = $port;
		$this->ports = array_unique ( $this->ports );
		return true;
	}
	function enableSSL($e, $cert, $key, $ssl_redirect = 0) {
		if (! file_exists ( $cert ) || ! file_exists ( $key )) {
			$this->SSL = false;
			return false;
		}
		$this->SSL = ( bool ) $e;
		$this->SSL_KEY = $key;
		$this->SSL_CERT = $cert;
		$this->SSL_ROUTE = $ssl_redirect;
		return true;
	}
	function enableKA($num){
		$this->keepalive = $num;
	}
	function enableCache($cache = true) {
		$this->Cache = $cache;
	}
	function innerBuild($port,$ssl=false,$port_to=false){
		$ret = "server {\n";
		$port = (int)$port;
		if(!$port){
			$port = '80';
		}
		if(!$port_to){
			$port_to = $port;
		}
		$ret .= "listen " . self::IP ( $this->ip_addr ) . ":".$port." ".($ssl?'ssl':'').";\n";
		
		if (!filter_var ( $this->ip_addr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 )) {
			$ret .= "proxy_bind ".$this->ip_addr.";\n";
		}
		
		if ($ssl) {
			$ret .= "ssl_certificate " . $this->SSL_CERT . ";\n";
			$ret .= "ssl_certificate_key " . $this->SSL_KEY . ";\n";
		}
		if ($this->Cache) {
			$ret .= "proxy_cache zcache;\n";
			$ret .= "proxy_cache_key \"" . $port . "\$host " . $port_to . "\$request_uri \$scheme " . $this->ip_addr . "\";\n";
		}
		
		if($this->keepalive){
			$ret .= "proxy_http_version 1.1;\n";
    		$ret .= "proxy_set_header Connection \"\";\n";
    		$ret .= "proxy_buffering off;\n";
		}
    	
		$upstream = $this->buildUpstream($this->to_addr,$port_to);
		$ret .= "location / {\n";
		if ($port_to == 443) {
			$ret .= "proxy_pass https://".$upstream['name'].";\n";
		} else {
			$ret .= "proxy_pass http://".$upstream['name'].";\n";
		}
		$ret .= "proxy_set_header X-Real-IP \$remote_addr;\n";
		$ret .= "proxy_set_header X-Scheme \$scheme;\n";
		$ret .= "proxy_set_header Host \$host;\n";
		$ret .= "}\n";
		
		$ret .= "}\n";
		return $upstream['block'].$ret;
	}
	function buildUpstream($to,$port){
		$hash = array($this,$to,$port,$this->upstream_count++);
		
		$name = 'u'.md5(serialize($hash));
		
		$ret =  'upstream '.$name.' {'."\nserver ".$to . ":" . $port . ";\n";
		if($this->keepalive){
			$ret .= "keepalive ".$this->keepalive.";\n";
		}
		$ret .= '}'."\n\n";
		
		return array('name'=>$name,'block'=>$ret);
	}
	function BuildConfig() {
		if (! $this->ip_addr || ! $this->to_addr) {
			return '';
		}
		$ret = '';
		foreach ( $this->ports as $p ) {
			$ret .= $this->innerBuild($p);
			if($this->SSL){
				$port_to = $p;
				if($this->SSL_ROUTE == self::ROUTE_PORT80){
					$port_to = 80;
				}
				$ret .= $this->innerBuild(443,true,$port_to);
			}
		}
		
		
		return $ret;
	}
}
?>