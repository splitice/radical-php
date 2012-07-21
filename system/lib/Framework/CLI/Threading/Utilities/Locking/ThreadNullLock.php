<?php

namespace CLI\Threading\Utilities\Locking;

class ThreadNullLock {
	function lock($callback) {
		return $callback ();
	}
}