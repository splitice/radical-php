<?php
namespace Basic\Cryptography\HashTypes;

interface ITwoWayEncryption extends IEncode {
	static function Decode($text, $key = null);
	static function Encode($text, $key = null);
	//PHP Strangeness?
}