<?php

namespace CLI\Threading\Utilities\Pulse;

// TODO: 1 queue, multiple message types
class QueuePulse {
	private $queue;
	static $count = 0;
	static $freed = array (); // TODO: free'd ids
	function __construct() {
		$this->queue = msg_get_queue ( static::$count ++ );
		if (! $this->queue) {
			throw new \Exception ( "Couldnt create message queue" );
		}
	}
	function Pulse() {
		msg_send ( $this->queue, 1, "", false );
	}
	function Wait() {
		msg_receive ( $this->queue, 1, $type, 3, $message, false );
	}
	function __destruct() {
		msg_remove_queue ( $this->queue );
	}
}