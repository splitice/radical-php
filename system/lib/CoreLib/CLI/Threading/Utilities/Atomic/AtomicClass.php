<?php

namespace CLI\Threading\Utilities\Atomic;

abstract class AtomicClass {
	protected function _atomic($var, $default = null) {
		$ref = get_called_class () . '/' . $var;
		if ($default === null && isset ( $this->$var )) {
			$default = $this->$var;
		}
		$this->$var = new AtomicVariable ( $ref, $default );
	}
}