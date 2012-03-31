<?php
namespace Basic\Cryptography;

use Basic\Cryptography\Internal\HashBase;

class CRC32 extends HashBase implements HashTypes\IOneWayHash {
	static function Hash($text){
		$r = crc32 ( $s );
		if (PHP_INT_SIZE == 8 && ($r > 0x7FFFFFFF)) {
			$r -= 0x100000000;
		}
		return $r;
	}
}