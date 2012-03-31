<?php
namespace Net\ExternalInterfaces\ContentAPI\Internal;

abstract class ConfigBase extends \Core\Object {
	const CACHE = null;
	const REMOTE = null;
	
	static function getCache(){
		if(static::CACHE){
			$cache = '\\Net\ExternalInterfaces\\ContentAPI\\Cache\\'.static::CACHE;
			return $cache;
		}
	}
}