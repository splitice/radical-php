<?php

namespace CLI\Threading\Utilities\Native;

use CLI\Threading\Internal\SemaphoreHelpers;
use CLI\Threading\Internal\ThreadReference;

class Semaphore extends SemaphoreHelpers {
	private $sem;
	function __construct($key = null, $max = 10) {
		if ($key === null) {
			$key = rand ( PHP_INT_MAX * - 1, PHP_INT_MAX );
		}
		$this->sem = sem_get ( $key, $max );
		$this->ref = new ThreadReference ();
	}
	function Acquire() {
		sem_acquire ( $this->sem );
	}
	function Release() {
		sem_release ( $this->sem );
	}
	function __destruct() {
		$sem = $this->sem;
		$this->ref->dec ( function ($v) use($sem) {
			sem_remove ( $sem );
		} );
	}
}