<?php
namespace Web\Page;

class RequestURL extends \Utility\Net\URL {
	private $localPath;
	
	function __construct($data){
		parent::__construct($data);
		global $WEBPATH;
		$wLen = strlen($WEBPATH);
		if($wLen == 0){
			$this->localPath = $this->path;
		}else{
			$p = $this->path->getPath(true);
			if(substr($p,0,$wLen) == $WEBPATH){
				$p = substr($p,$wLen);
				$this->localPath = clone $this->path;
				$this->localPath->setPath($p);
			}
		}
	}
	function getPath(){
		return $this->localPath;
	}
	function getRealPath(){
		return parent::getPath();
	}
	function __clone(){
		$this->localPath = clone $this->localPath;
		parent::__clone();
	}
}