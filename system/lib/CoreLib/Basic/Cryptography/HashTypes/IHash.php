<?php
namespace Basic\Cryptography\HashTypes;

interface IHash {
	static function Hash($text);
	static function Compare($text,$hash);
}
