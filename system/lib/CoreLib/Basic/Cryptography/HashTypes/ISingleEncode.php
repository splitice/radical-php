<?php
namespace Basic\Cryptography\HashTypes;

interface ISingleEncode extends IEncode {
	static function Encode($text);
}