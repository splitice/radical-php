<?php
namespace Cache\Object\Internal;

abstract class FileCacheBase extends CacheBase {
	private $cachePath;
	
	protected function CachePath(){
		if($this->cachePath){
			return $this->cachePath;
		}
		$this->cachePath = realpath(__DIR__.DS.'..'.DS.'..'.DS.'..'.DS.'cache');
		return $this->cachePath;
	}
}