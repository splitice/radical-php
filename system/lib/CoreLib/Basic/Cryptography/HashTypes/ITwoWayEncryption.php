<?php
namespace Basic\Cryptography\HashTypes;

interface ITwoWayEncryption extends IEncode {
	static function Decode($text, $key);
	static function Encode($text, $key);
	//PHP Strangeness?
}