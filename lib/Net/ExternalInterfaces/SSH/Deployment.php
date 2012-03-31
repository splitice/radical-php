<?php
namespace Net\ExternalInterfaces\SSH;

class Deployment extends Connection {
	protected $path;
	
	function __construct($host,$port,$path){
		parent::__construct($host,$port);
		$this->path = $path;
	}
	function getPath(){
		return parent::getPath($this->path);
	}
	static function fromArray(array $in){
		$r = new static($in['host'],$in['port'],$in['path']);
		if(isset($in['username']) && isset($in['password'])){
			$r->authenticate->Password($in['username'], $in['password']);
		}
		return $r;
	}
}