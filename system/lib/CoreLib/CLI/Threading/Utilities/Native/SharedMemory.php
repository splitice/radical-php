<?php

namespace CLI\Threading\Utilities\Native;

abstract class SharedMemory {
	protected $key;
	protected $id;
	function __construct($key = null) {
		if ($key == null) {
			$key = tempnam ( '/tmp', 'PHP' );
		}
		
		$this->key = ftok ( $key, 'a' );
		$this->id = shm_attach ( $this->key );
		
		if (! $this->id)
			throw new Exception ( 'Unable to create shared memory segment' );
	}
	function __sleep() {
		shm_detach ( $this->id );
	}
	function __destruct() {
		shm_detach ( $this->id );
	}
	function __wakeup() {
		$this->id = sem_get ( $this->key );
		shm_attach ( $this->id );
	}
	function getKey() {
		return $this->key;
	}
	function destroy() {
		shm_remove ( $this->id );
	}
}