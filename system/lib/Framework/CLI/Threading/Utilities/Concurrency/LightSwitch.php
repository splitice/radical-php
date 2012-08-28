<?php

namespace CLI\Threading\Utilities\Concurrency;

use CLI\Threading\Utilities\Atomic\AtomicClass;
use CLI\Threading\Utilities\Native;

class LightSwitch extends AtomicClass {
	private $sem;
	protected $count;
	function __construct($sem = null) {
		if ($sem === null)
			$sem = new Native\Semaphore ();
		
		$this->sem = $sem;
		$this->count = $this->_atomic ( 'count' );
	}
	function enter() {
		$count = null;
		$this->count->update ( function ($val) use(&$count) {
			$count = $val;
			return $val + 1;
		} );
		if (! $count) {
			$this->sem->Acquire ();
		}
	}
	function leave() {
		$sem = $this->sem;
		$this->count->update ( function ($val) use($sem) {
			if ($val == 0)
				throw new \Exception ( "Too many threads leaving a room" );
			
			if ($val == 1)
				$sem->Release ();
			
			return $val - 1;
		} );
	}
}