<?php
namespace CLI\Threading;

class Thread {
	private static function _init() {
		if (! self::$init) {
			self::$init = true;
			self::$current = new Thread ( getmypid () );
		}
	}
	private static $init = false;
	private static $current;
	private static $refs = array ();
	public $children = array ();
	private static function _ref($new) {
		foreach ( self::$refs as $r ) {
			$r->incr ();
			if ($new)
				$this->onThread ();
		}
	}
	function addRef($ref) {
		$this->refs [] = $ref;
	}
	function __construct($pid = null) {
		self::_init ();

		// What am I?
		if ($pid === null) {
			$parent = self::$self;
			
			// Thread
			$pid = pcntl_fork ();
			
			if ($pid == false) { // Child
			                   // This is the thread
				$this->parent = $parent;
				$this->pid = getmypid ();
				static::$self = $this;
			} elseif ($pid) {
				// This is the parent
				$this->pid = $pid;
				
				// Store as child
				self::$self->children [$pid] = $this;
			}
		} else {
			// Creating an object for use as reference -- INIT
			$this->pid = $pid;
		}
		
		// Reference tracking
		self::_ref ( $pid !== null );
	}
	function isThis() {
		if ($this->pid == getmypid ()) {
			return true;
		}
		return false;
	}
	function setName(){
		$this->name = $name;
		if(function_exists('setproctitle')){
			global $_SCRIPT_NAME;
			if(!isset($_SCRIPT_NAME)){
				return false;
			}
			setproctitle($_SCRIPT_NAME . ' ['.$name.']');
			return true;
		}
		return false;
	}
	static function current() {
		self::_init ();
		return self::$current;
	}
	
	function Sleep($time){
		Sleep($time);
	}
}