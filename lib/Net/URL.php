<?php
namespace Net;
use \Basic\String\UTF8;

class URL extends \Core\Object {
	private $scheme;
	private $host;
	/**
	 * @var URL\Path
	 */
	private $path;
	
	static private function _SCHEME_VALID($url) {
		$scheme = strtolower ( ( string ) parse_url ( $url, PHP_URL_SCHEME ) );
		if ($scheme == 'http' || $scheme == 'https' || $scheme == 'ftp' || $scheme == 'ftps') {
			return true;
		}
		return false;
	}
	function __construct($data) {
		$this->scheme = $data ['scheme'];
		$this->host = $data ['host'];
		
		$path = isset($data ['path'])?$data ['path']:'/';
		$query = isset($data ['query'])?$data ['query']:null;
		if($query){
			parse_str($query,$query);
		}
		
		$fragment = isset($data ['fragment'])?$data ['fragment']:null;
		
		$this->path = URL\Path::fromSplit($path,$query,$fragment);
	}
	
	function isSubDomainOf($domain) {
		if (! ($domain instanceof URL)) {
			$domain = self::fromURL ( $domain );
		}
		$domain_host = $domain->getHost ();
		$len = strlen ( $domain_host );
		
		if (strtolower(substr ( $this->getHost (), $len * - 1 )) == strtolower($domain_host)) {
			return true;
		}
		return false;
	}
	function isPartOfDomain($subdomain) {
		if (! ($subdomain instanceof URL)) {
			$subdomain = self::fromURL ( $subdomain );
		}
		return $subdomain->isSubDomainOf ( $this );
	}
	function domainParts(){
		$ret = array();
		$p = explode('.',$this->getHost());
		$c = count($p);
		foreach($p as $k=>$v){
			$domain = implode('.',array_slice($p,$k,$c-$k));
			$ret[] = $domain;
		}
		return $ret;
	}
	
	function toURL() {
		$url = $this->scheme . '://' . $this->host . '/' . ltrim($this->path->__toString(),'/');
		return rtrim($url,'/');
	}
	
	function __toString() {
		return $this->toURL ();
	}
	
	function __clone(){
		$this->path = clone $this->path;
	}
	
	/* Getters/Setters */
	/**
	 * @return the $scheme
	 */
	public function getScheme() {
		return $this->scheme;
	}
	
	/**
	 * @return the $host
	 */
	public function getHost() {
		return $this->host;
	}
	
	/**
	 * @return \Net\URL\Path $path
	 */
	public function getPath() {
		return $this->path;
	}
	
	/**
	 * @return the $query
	 */
	public function getQuery() {
		return $this->path->getQuery();
	}
	
	/**
	 * @return the $fragment
	 */
	public function getFragment() {
		return $this->path->getFragment();
	}
	
	/**
	 * @param field_type $scheme
	 */
	public function setScheme($scheme) {
		$this->scheme = $scheme;
	}
	
	/**
	 * @param field_type $host
	 */
	public function setHost($host) {
		$this->host = $host;
	}
	
	/**
	 * @param field_type $path
	 */
	public function setPath($path) {
		$this->path = $path;
	}
	
	/**
	 * @param field_type $query
	 */
	public function setQuery($query) {
		$this->query = $query;
	}
	
	/**
	 * @param field_type $fragment
	 */
	public function setFragment($fragment) {
		$this->fragment = $fragment;
	}
	
	/* Virtual Construct */
	static function fromURL($url) {
		if (! strpos ( $url, '://' ) || ! self::_SCHEME_VALID ( $url )) {
			$url = 'http://' . $url;
		}
		$ret = parse_url ( $url );
		if (isset ( $ret ['scheme'] )) {
			$ret ['scheme'] = UTF8::lower ( $ret ['scheme'] );
			$ret ['host'] = UTF8::lower ( $ret ['host'] );
			return new URL ( $ret );
		}
		return false;
	}
	
	static function fromRequest($path = null){
		$scheme = 'http://';
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']){
			$scheme = 'https://';
		}
		
		$url = $scheme.$_SERVER["HTTP_HOST"];
		if(!$path) $path = $_SERVER['REQUEST_URI'];
		$url.=$path;
		
		return static::fromURL($url);
	}
}