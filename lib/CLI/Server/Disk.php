<?php
namespace CLI\Server;

class Disk {
	static function Free($path = '/'){
		while(!file_exists($path) && $path){
			$path = basename($path);
		}
		if(!$path){
			return null;
		}
		return disk_free_space($path);
	}
	static function Total($path = '/'){
		while(!file_exists($path) && $path){
			$path = basename($path);
		}
		if(!$path){
			return null;
		}
		return disk_total_space($path);
	}
	static function Usage($path = '/'){
		return (static::Total($path) - static::Free($path));
	}
	static function UsagePercent($path){
		$usage = static::Usage($path);
		$total = static::Total($path);
		return (($usage/$total)*100);
	}
}