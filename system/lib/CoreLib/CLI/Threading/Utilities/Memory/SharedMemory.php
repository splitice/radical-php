<?php

namespace CLI\Threading\Utilities\Memory;

class SharedMemory extends PersistantMemory {
	const SHM_REFCOUNT = 1;
	const SHM_DATA = 2;
	private $refcount;
	function __construct($key = null) {
		$this->refcount = Mutex::fromObject ( $this );
		$new = parent::__construct ( $key );
		$this->_instanceCount ( $new );
	}
	function __destruct() {
		if (shm_get_var ( $this->id, static::SHM_REFCOUNT ) == 1) {
			// I am the last listener so kill shared memory space
			$this->destroy ();
		} else {
			$this->_instanceCount ( null, true );
			parent::__destruct ();
		}
	}
	private function _instanceCount($new = null, $destroy = false) {
		// Get a lock so we can perform non atomic operations on the refcount
		$this->refcount->Acquire ();
		
		if ($new) {
			shm_put_var ( $this->id, static::SHM_REFCOUNT, 1 );
		} else {
			$m = 1;
			if ($destroy) {
				$m = - 1;
			}
			
			// Increment / Decrement the refcount
			shm_put_var ( $this->id, static::SHM_REFCOUNT, shm_get_var ( $this->id, static::SHM_REFCOUNT ) + $m );
		}
		
		$this->refcount->Release ();
	}
	function __wakeup() {
		parent::__wakeup ();
		$this->_instanceCount ( false );
	}
}