<?php
namespace Basic\Cryptography\HashTypes;

interface ISingleDecode extends IDecode {
	static function decode($encText);
}