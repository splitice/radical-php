<?php
namespace Web\Resource;

use Core\Server;

abstract class ResourceBase {
	const PATH = '|';
	
	static function Path($name){
		global $BASEPATH;
		return $BASEPATH.DIRECTORY_SEPARATOR.'*'.DIRECTORY_SEPARATOR.static::PATH.DIRECTORY_SEPARATOR.$name;
	}
	static function FileTime($name){
		$filemtime = 0;
		foreach(glob(static::Path($name)) as $dirs){
			foreach(glob($dirs.DIRECTORY_SEPARATOR.'*') as $file){
				$filemtime = max($filemtime, filemtime($file));
			}
		}
		return $filemtime;
	}
	static function Exists($name){
		return (count(glob(static::Path($name))) > 0);
	}
	protected static abstract function _HTML($path);
	static function HTML($name){
		return static::_HTML(Server::getSiteRoot().$name.'.'.static::FileTime($name).'.'.static::PATH);
	}
}