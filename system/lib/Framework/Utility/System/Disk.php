<?php
namespace Utility\System;

class Disk {
	static function free($path = '/'){
		while(!file_exists($path) && $path){
			$path = basename($path);
		}
		if(!$path){
			return null;
		}
		return disk_free_space($path);
	}
	static function total($path = '/'){
		while(!file_exists($path) && $path){
			$path = basename($path);
		}
		if(!$path){
			return null;
		}
		return disk_total_space($path);
	}
	static function usage($path = '/'){
		return (static::Total($path) - static::Free($path));
	}
	static function usagePercent($path){
		$usage = static::Usage($path);
		$total = static::Total($path);
		return (($usage/$total)*100);
	}
}