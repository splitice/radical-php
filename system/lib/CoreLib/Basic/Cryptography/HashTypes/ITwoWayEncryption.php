<?php
namespace Basic\Cryptography\HashTypes;

interface ITwoWayEncryption {
	static function Decode($text, $key);
	static function Encode($text, $key);
}