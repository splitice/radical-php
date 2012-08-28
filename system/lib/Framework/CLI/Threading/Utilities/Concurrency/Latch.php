<?php

namespace CLI\Threading\Utilities\Concurrency;

class Latch {
	private $turnstile;
	function __construct() {
		$this->turnstile = new Semaphore ();
	}
	function wait() {
		$this->turnstile->Acquire ();
		$this->turnstile->Release ();
	}
	function open() {
		$this->turnstile->Release ();
	}
}