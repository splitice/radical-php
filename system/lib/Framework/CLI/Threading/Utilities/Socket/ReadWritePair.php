<?php

namespace CLI\Threading\Utilities\Socket;

class ReadWritePair extends BiDirectionalPair {
	function write($msg) {
		return parent::Write ( 0, $msg );
	}
	function read($len) {
		return parent::Read ( 1, $len );
	}
}