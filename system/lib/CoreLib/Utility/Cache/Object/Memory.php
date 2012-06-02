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
		$key = $this->key($key);
		if (function_exists ( 'apc_fetch' )) {
			if(apc_exists($key)){
				return apc_fetch ( $key );
			}
		}
		if (function_exists ( 'xcache_get' )) {
			if(xcache_isset($key)){
				return xcache_get ( $key );
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
		$key = $this->key($key);
		if (function_exists ( 'apc_store' )) {
			return apc_store ( $key, $value, $ttl );
		}
		if (function_exists ( 'xcache_set' )) {
			return xcache_set ( $key, $value, $ttl );
		}
	}
	
	function Delete($key){
		$key = $this->key($key);
		if (function_exists ( 'apc_delete' )) {
			return apc_delete ( $key );
		}
		if (function_exists ( 'xcache_unset' )) {
			return xcache_unset ( $key );
		}
	}
}