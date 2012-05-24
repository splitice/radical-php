<?php

namespace CLI\Threading\Utilities\Locking;

class ThreadLock {
	private $mutex;
	function __construct($shm, $key) {
		$keyHash = crc32 ( $key ) ^ $shm;
		$this->mutex = new Mutex ( $keyHash );
	}
	function lock($callback) {
		$this->mutex->Acquire ();
		
		$ret = $callback ();
		
		$this->mutex->Release ();
		
		return $ret;
	}
}