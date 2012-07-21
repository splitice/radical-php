<?php

namespace CLI\Threading\Utilities\Concurrency;

use CLI\Threading\Utilities\Atomic\AtomicClass;

class Exchanger extends AtomicClass {
	private $turnstile;
	private $handshake;
	protected $isFirst = true;
	protected $item;
	function __construct() {
		$this->turnstile = new Semaphore ( 1 );
		$this->handshake = new Semaphore ();
		$this->_atomic ( 'isFirst' );
		$this->_atomic ( 'item' );
	}
	function Exchange($object) {
		$item = $this->item;
		$turnstile = $this->turnstile;
		$handshake = $this->handshake;
		$ret = $this->isFirst->lock ( function ($h, $v) use($item, $object, $turnstile, $handshake) {
			$isFirst = $h->$v;
			if ($isFirst) {
				$item->write ( $object );
				$h->$v = false;
				$turnstile->Release ();
			} else {
				$ret = $item->get ();
				$item->write ( $object );
				$handshake->Release ();
				return $ret;
			}
		} );
		
		if ($ret)
			return $ret;
		
		$handshake->Acquire ();
		
		return $this->isFirst->lock ( function ($h, $v) use($item, $turnstile) {
			$h->$v = true;
			$turnstile->Release ();
			return $item->get ();
		} );
	}
}