<?php
namespace Basic\Cryptography;

use Basic\Cryptography\Internal\HashBase;

class MD5 extends HashBase implements HashTypes\IOneWayHash {
	static function Hash($text){
		return md5($text);
	}
}