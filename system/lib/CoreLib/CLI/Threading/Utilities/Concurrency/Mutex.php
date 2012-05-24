<?php

namespace CLI\Threading\Utilities\Concurrency;

use CLI\Threading\Utilities\Native;

class Mutex extends Native\Semaphore {
	function __construct($key) {
		return parent::__construct ( $key, 1 );
	}
}