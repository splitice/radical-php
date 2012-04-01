<?php
namespace Cache\Object;

class FileCache extends Internal\FileCacheBase implements ICache {
	const PATH = 'main';

	protected function path($key){
		$hash = md5($key);
		
		$dir = $this->CachePath();
		$parts = array(static::PATH,$hash{0},$hash{1},$hash{2},$hash{3});
		foreach($parts as $p){
			$dir .= DS.$p;
			if(!file_exists($dir)){
				@mkdir($dir);
			}
		}
		
		$path = $dir.DS.substr($hash,4);
		if(!@file_exists($path)){
			return false;
		}
		
		return $path;
	}
	function Get($key){
		if($path = $this->path($key)){
			return file_get_contents($path);
		}
	}
	function Set($key,$value,$ttl = null){
		$path = $this->path($key);
		if($path){
			@file_put_contents($path, $value);
		}
	}
	function Delete($key){
		$path = $this->path($key);
		if($path){
			@unlink($path);
		}
	}
}