<?php

namespace CLI\Threading\Utilities\Concurrency;

use CLI\Threading\Utilities\Atomic\AtomicClass;
use CLI\Threading\Utilities\Native;

class Barrier extends AtomicClass {
	private $latch;
	private $arrived = 0;
	protected $count;
	private $goPerm;
	function __construct($tickets) {
		$this->count = $tickets;
		$this->_atomic ( 'arrived' );
		$this->latch = new Semaphore ();
		$this->goPerm = new Native\Semaphore ( null, 1 );
	}
	function Acquire() {
		// Spin goperm
		$this->goPerm->Acquire ();
		$this->goPerm->Release ();
		
		// Increment and get _arrived
		$a = null;
		$this->arrived->update ( function ($val) use(&$a) {
			$a = $val + 1;
			return $a;
		} );
		
		//
		if ($a != $this->count) {
			$this->latch->Acquire ();
		} else {
			$this->goPerm->Acquire ();
			$this->latch->Release ( $this->count - 1 );
		}
		
		// Release
		$goPerm = $this->goPerm;
		$this->arrived->update ( function ($arrived) use($goPerm) {
			if ($arrived == 1) {
				$goPerm->Release ();
			}
			return $arrived - 1;
		} );
	}
}