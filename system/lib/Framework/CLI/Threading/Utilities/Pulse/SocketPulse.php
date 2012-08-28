<?php
namespace CLI\Threading\Utilities\Pulse;

use CLI\Threading\Utilities\Socket;

class SocketPulse {
	private $communication;
	function __construct() {
		$this->communication = new Socket\ReadWritePair ();
	}
	function pulse() {
		$this->communication->Write ( '.' );
	}
	function wait() {
		$this->communication->Read ( 1 );
	}
}