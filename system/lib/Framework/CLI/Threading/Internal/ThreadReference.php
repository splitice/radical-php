<?php
namespace CLI\Threading\Internal;

use CLI\Threading\Thread;
use CLI\Threading\Utilities\Atomic\AtomicClass;

class ThreadReference extends AtomicClass {
	protected $refs;
	function __construct($refs = 1) {
		$this->_atomic ( 'refs', $refs );
		Thread::current ()->addRef ( $this );
	}
	function inc() {
		$this->refs->inc ();
	}
	function onThread() {
	}
	function dec($destroy = null) {
		if ($destroy === null) {
			$this->refs->dec ();
		} else {
			$this->refs->update ( function ($refs) use($destroy) {
				if ($refs == 1)
					$destroy ();
				
				return $refs - 1;
			} );
		}
	}
}