<?php

namespace CLI\Threading\Utilities\Concurrency;

class Semaphore extends SemaphoreHelpers {
	const BUISY_WAIT_INT = 1;
	private $tickets;
	private $message;
	function __construct($tickets = 0, $pulseAdapter = null) {
		$id = crc32 ( spl_object_hash ( $this ) );
		
		$this->tickets = new AtomicVariable ( $id, $tickets );
		
		if ($pulseAdapter === null) {
			$pulseAdapter = new ThreadPulse ();
		}
		$this->message = $pulseAdapter;
		
		while ( $tickets ) {
			$this->message->Pulse ();
			$tickets --;
		}
	}
	function Acquire($tickets = 1) {
		$cont = true;
		/*
		 * Buisy Wait while($cont){ $this->tickets->update(function($value)
		 * use($tickets,&$cont){ if($value >= $tickets){ $value -= $tickets;
		 * $cont = false; } return $value; }); if($cont)
		 * Sleep(self::BUISY_WAIT_INT); }
		 */
		while ( true ) {
			$this->message->Wait ();
			$this->tickets->update ( function ($value) use($tickets, &$cont) {
				if ($value >= $tickets) {
					$value -= $tickets;
					$cont = false;
				}
				return $value;
			} );
			if (! $cont)
				return;
		}
	}
	function Release($tickets = 1) {
		$this->tickets->inc ( $tickets );
		while ( $tickets ) {
			$this->message->Pulse ();
			$tickets --;
		}
	}
}