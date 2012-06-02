<?php
namespace Utility\Net\External\ContentAPI\Internal;

abstract class ConfigBase extends \Core\Object {
	const CACHE = null;
	const REMOTE = null;
	
	static function getCache(){
		if(static::CACHE){
			$cache = '\\Utility\Net\External\\ContentAPI\\Cache\\'.static::CACHE;
			return $cache;
		}
	}
}