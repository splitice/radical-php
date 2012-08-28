<?php
namespace Utility\Cache\Object;

class FileCache extends Internal\FileCacheBase implements ICache {
	const BASE = 'cache';
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
		return $dir.DS.substr($hash,4);
	}
	function get($key){
		if($path = $this->path($key)){
			if(!@file_exists($path)){
				return null;
			}
			return file_get_contents($path);
		}
	}
	function set($key,$value,$ttl = null){
		$path = $this->path($key);
		if($path){
			$success = (@file_put_contents($path, $value) !== false);
			if(!$success)
				throw new \Exception('Couldnt Write file for cache: '.$path);
		}
	}
	function delete($key){
		$path = $this->path($key);
		if($path){
			@unlink($path);
		}
	}
}