<?php
namespace Basic\Cryptography\Internal;

use Basic\Cryptography\HashTypes\IOneWayHash;

abstract class HashBase {
	static function encode($text,$key = null){
		return static::Hash($text);
	}
	static function compare($password,$hash){
		return ($password == $hash);
	}
}