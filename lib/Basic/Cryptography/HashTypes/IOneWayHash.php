<?php
namespace Basic\Cryptography\HashTypes;

interface IOneWayHash extends IEncode {
	static function Hash($text);
	static function Compare($text,$hash);
}