<?php

namespace CLI\Threading\Utilities\Atomic;

use CLI\Threading\Utilities\Locking\ThreadLock;
use CLI\Threading\Utilities\Locking\ThreadNullLock;
use CLI\Threading\Utilities\Memory\SharedMemory;

class AtomicMemory extends SharedMemory {
	private $_locked;
	public function _enterLock() {
		$this->_locked = true;
	}
	public function _leaveLock() {
		$this->_locked = false;
	}
	function lock($var, $callback) {
		// Create callback
		$storage = $this;
		$callback = function () use($callback, $var, $storage) {
			$storage->_enterLock ();
			$ret = $callback ( $storage, $var );
			$storage->_leaveLock ();
			return $ret;
		};
		
		// Lock and execute
		return $this->_getLock ( $var )->lock ( $callback );
	}
	protected function _getLock($var) {
		if ($this->_locked)
			return new ThreadNullLock ();
		return new ThreadLock ( $this->id, $var );
	}
	function get($var, $callback = null) {
		if ($callback == null)
			return $this->$var;
		
		return $this->lock ( $var, function ($storage, $var) use($callback) {
			return $callback ( $storage->$var );
		} );
	}
	function update($var, $callback) {
		$storage = $this;
		$this->get ( $var, function ($value) use($callback, $storage, $var) {
			$ret = $callback ( $value );
			// die(var_dump($ret));
			if ($ret !== $value)
				$storage->$var = $ret;
		} );
	}
	function set($var, $callback) {
		$storage = $this;
		$this->lock ( $var, function () use($callback, $storage, $var) {
			$storage->$var = $callback ();
		} );
	}
	function inc($var, $interval = 1) {
		$storage = $this;
		return $this->lock ( $var, function () use($var, $interval, $storage) {
			$storage->$var += $interval;
		} );
	}
	function dec($var, $interval) {
		return $this->inc ( $var, - 1 * $interval );
	}
}