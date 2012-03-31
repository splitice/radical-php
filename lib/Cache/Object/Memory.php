<?php
namespace Cache\Object;

/**
 * Persistant Memory Cache System
 * @author SplitIce
 *
 */
class Memory extends Internal\CacheBase implements ICache {
	/**
	 * Get a value from memory using $key
	 * @param string $key Key the value is stored as
	 * @return mixed
	 */
	function Get($key) {
		if (function_exists ( 'apc_fetch' )) {
			$key = $this->key($key);
			if(apc_exists($key)){
				return apc_fetch ( $key );
			}
		}
		return null;
	}
	
	/**
	 * Insert or Update a value in memory using $key
	 * @param string $key Key to store value as
	 * @param mixed $value The Value to store
	 * @param int $ttl Time to cache in memory for
	 */
	function Set($key, $value, $ttl = 3600) {
		if (function_exists ( 'apc_store' )) {
			$key = $this->key($key);
			return apc_store ( $key, $value, $ttl );
		}
	}
}