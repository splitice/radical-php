<?php

namespace CLI\Threading\Utilities\Concurrency;

use CLI\Threading\Utilities\Atomic\AtomicClass;

class Channel extends AtomicClass {
	private $sem;
	protected $queue;
	function __construct($max = 10) {
		if ($id == null)
			$id = spl_object_hash ( $this );
		if (! is_numeric ( $id ))
			$id = crc32 ( $id );
		
		$this->sem = new Semaphore ( 0 );
		$this->_atomic ( 'queue', array () );
	}
	function put($item) {
		$this->queue->update ( function ($var) use($item) {
			$var [] = $item;
			return $var;
		} );
		
		$this->sem->Release ();
	}
	function take() {
		$ret = null;
		
		$this->sem->Acquire ();
		
		$this->queue->update ( function ($var) use(&$ret) {
			$ret = array_shift ( $var );
			return $var;
		} );
		
		return $ret;
	}
}