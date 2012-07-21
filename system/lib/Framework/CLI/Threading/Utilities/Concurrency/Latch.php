<?php

namespace CLI\Threading\Utilities\Concurrency;

class Latch {
	private $turnstile;
	function __construct() {
		$this->turnstile = new Semaphore ();
	}
	function Wait() {
		$this->turnstile->Acquire ();
		$this->turnstile->Release ();
	}
	function Open() {
		$this->turnstile->Release ();
	}
}