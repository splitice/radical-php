<?php
namespace Basic\Cryptography\HashTypes;

interface IHash {
	static function hash($text);
	static function compare($text,$hash);
}
