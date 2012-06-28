<?php
namespace Basic\Cryptography\Internal;

use Basic\Cryptography\HashTypes\IOneWayHash;

abstract class HashBase implements IOneWayHash {
	static function Encode($text){
		return static::Hash($text);
	}
	static function Compare($password,$hash){
		return ($password == $hash);
	}
}