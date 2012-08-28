<?php
namespace CLI\Threading\Utilities\Concurrency;

use CLI\Threading\Utilities\Atomic\AtomicClass;

/**
 * Bounded(1) version of Channel
 *
 * @author SplitIce
 *        
 */
class Messanger extends AtomicClass {
	private $sem;
	private $data;
	function __construct($max = 10, $id = null) {
		if ($id == null)
			$id = spl_object_hash ( $this );
		if (! is_numeric ( $id ))
			$id = crc32 ( $id );
		
		$this->sem = new Semaphore ( 0 );
		$this->_atomic ( 'data', $id );
	}
	function put($item) {
		$this->data->update ( function ($var) use($item) {
			return $item;
		} );
		
		$this->sem->Release ();
	}
	function take() {
		$ret = null;
		
		$this->sem->Acquire ();
		
		$this->data->update ( function ($var) use(&$ret) {
			$ret = $var;
			return null;
		} );
		
		return $ret;
	}
}