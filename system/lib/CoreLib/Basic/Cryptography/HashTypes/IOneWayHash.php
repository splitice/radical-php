<?php
namespace Basic\Cryptography\HashTypes;

interface IOneWayHash extends ISingleEncode {
	static function Hash($text);
	static function Compare($text,$hash);
}