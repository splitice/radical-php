<?php
namespace Basic\Cryptography;

use Basic\Cryptography\Internal\HashBase;

class Raw extends HashBase implements HashTypes\ITwoWayEncryption, HashTypes\IOneWayHash {
	static function Hash($text){
		return $text;
	}
	static function Decode($text){
		return $text;
	}
}