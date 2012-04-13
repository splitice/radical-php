<?php
namespace Web\Resource;

abstract class ResourceBase {
	const PATH = '|';
	
	static function Path($name){
		global $BASEPATH;
		return $BASEPATH.DIRECTORY_SEPARATOR.'*'.DIRECTORY_SEPARATOR.static::PATH.DIRECTORY_SEPARATOR.$name;
	}
	static function Exists($name){
		return (count(glob(static::Path($name))) > 0);
	}
	protected static abstract function _HTML($path);
	static function HTML($name){
		return static::_HTML($name.'.0.'.static::PATH);
	}
}