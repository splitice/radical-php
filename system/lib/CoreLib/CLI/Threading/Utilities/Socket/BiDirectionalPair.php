<?php

namespace CLI\Threading\Utilities\Socket;

class BiDirectionalPair {
	private $sockets;
	private $ref;
	function __construct() {
		$this->sockets = socket_create ( AF_UNIX, SOCK_STREAM, SOL_TCP );
		$this->ref = new ThreadReference ();
	}
	function Write($socket, $msg) {
		socket_write ( $this->sockets [$socket], $msg );
	}
	function Read($socket, $len) {
		return socket_read ( $this->sockets [$socket], $len );
	}
	function __destruct() {
		$this->ref->dec ( function () {
			socket_close ( $this->sockets [0] );
			socket_close ( $this->sockets [1] );
		} );
	}
}