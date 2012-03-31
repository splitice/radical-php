<?php
namespace Basic\Cryptography\HashTypes;

interface IDecode extends IEncode {
	static function Decode($encText);
}