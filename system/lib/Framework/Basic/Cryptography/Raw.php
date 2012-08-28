<?php
namespace Basic\Cryptography;

use Basic\Cryptography\Internal\HashBase;

class Raw extends HashBase implements HashTypes\ITwoWayEncryption, HashTypes\IHash {
	static function hash($text){
		return $text;
	}
	static function decode($text,$key = null){
		return $text;
	}
}