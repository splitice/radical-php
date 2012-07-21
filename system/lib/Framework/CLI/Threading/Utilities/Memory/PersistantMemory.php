<?php
namespace CLI\Threading\Utilities\Memory;
use CLI\Threading\Utilities\Native;

class PersistantMemory extends Native\SharedMemory {
	const SHM_VARLIST = 0;
	const SHM_DATA = 1;
	protected $nameToKey = array ();
	function __construct($key = null) {
		parent::__construct ( $key );
		
		if (shm_has_var ( $this->id, static::SHM_VARLIST )) {
			$this->refreshMemoryVarList ();
			return false;
		} else {
			$this->updateMemoryVarList ();
			return true;
		}
	}
	function __wakeup() {
		parent::__wakeup ();
		$this->refreshMemoryVarList ();
	}
	function refreshMemoryVarList() {
		$this->nameToKey = shm_get_var ( $this->id, static::SHM_VARLIST );
	}
	function updateMemoryVarList() {
		shm_put_var ( $this->id, static::SHM_VARLIST, $this->nameToKey );
	}
	function __get($var) {
		if (! isset ( $this->nameToKey [$var] )) {
			$this->refreshMemoryVarList ();
		}
		if (! isset ( $this->nameToKey [$var] )) {
			throw new \Exception ( 'Cant get shared memory variable that doesnt exist' );
		}
		$ret = shm_get_var ( $this->id, $this->nameToKey [$var] );
		return $ret;
	}
	function __set($var, $val) {
		if (! isset ( $this->nameToKey [$var] )) {
			$this->refreshMemoryVarList ();
			$this->nameToKey [$var] = count ( $this->nameToKey ) + static::SHM_DATA;
			$this->updateMemoryVarList ();
		}
		$status = shm_put_var ( $this->id, $this->nameToKey [$var], $val );
		if ($status === false) {
			throw new \Exception ( 'SHM Put failed' );
		}
	}
	function __isset($key) {
		if (! isset ( $this->nameToKey [$key] )) {
			$this->refreshMemoryVarList ();
		}
		return isset ( $this->nameToKey [$key] );
	}
	function __unset($var) {
		if (isset ( $this->$key )) {
			shm_remove_var ( $this->id, $this->nameToKey [$var] );
		}
	}
}