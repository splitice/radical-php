<?php

namespace CLI\Threading\Utilities\Atomic;

class AtomicVariable {
	static $memory;
	private $var;
	function __construct($var, $default = null) {
		if (! static::$memory)
			static::$memory = new AtomicMemory ();
		
		$this->var = $var;
		
		$mem = static::$memory;
		// TODO: Mutex this
		if (! isset ( $mem->$var )) {
			$mem->$var = $default;
		}
	}
	function lock($callback) {
		return static::$memory->lock ( $this->var, $callback );
	}
	function get($callback = null) {
		return static::$memory->get ( $this->var, $callback );
	}
	function update($callback) {
		return static::$memory->update ( $this->var, $callback );
	}
	function set($callback) {
		return static::$memory->set ( $this->var, $callback );
	}
	function write($value) {
		$var = $this->var;
		static::$memory->$var = $callback;
	}
	function inc($value) {
		return static::$memory->inc ( $this->var, $value );
	}
	function dec($value) {
		return static::$memory->dec ( $this->var, $value );
	}
}