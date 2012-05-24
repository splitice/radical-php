<?php

namespace CLI\Threading\Utilities\Socket;

class ReadWritePair extends BiDirectionalPair {
	function Write($msg) {
		return parent::Write ( 0, $msg );
	}
	function Read($len) {
		return parent::Read ( 1, $len );
	}
}