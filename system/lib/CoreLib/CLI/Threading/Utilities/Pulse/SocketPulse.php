<?php

namespace CLI\Threading\Utilities\Pulse;

class SocketPulse {
	private $communication;
	function __construct() {
		$this->communication = new ReadWriteSocketPair ();
	}
	function Pulse() {
		$this->communication->Write ( '.' );
	}
	function Wait() {
		$this->communication->Read ( 1 );
	}
}