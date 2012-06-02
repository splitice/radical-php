<?php
namespace Cache\Object\Internal;

/**
 * Base class for all caches
 * @author SplitIce
 *
 */
abstract class CacheBase {
	protected $pool;
	
	function __construct($pool){
		$this->pool = $pool;
	}
	
	function key($key){
		return md5($key.'|'.$this->pool);
	}
	
	/**
	 * Function that checks if a key exists in the cache, if it doesnt executes a callback and stores it as $key 
	 * @param string $key_sem
	 * @param function $function
	 * @param int $ttl
	 * @return mixed
	 */
	function CachedValue($key_sem, $function, $ttl = 3600) {
		if ($data = $this->Get ( $key_sem )) {
			return $data;
		}
		$data = $function ();
		$this->Set ( $key_sem, $data, $ttl );
		return $data;
	}
}