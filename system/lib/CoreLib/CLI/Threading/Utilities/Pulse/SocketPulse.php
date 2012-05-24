<?php
namespace CLI\Threading\Utilities\Pulse;

use CLI\Threading\Utilities\Socket;

class SocketPulse {
	private $communication;
	function __construct() {
		$this->communication = new Socket\ReadWritePair ();
	}
	function Pulse() {
		$this->communication->Write ( '.' );
	}
	function Wait() {
		$this->communication->Read ( 1 );
	}
}