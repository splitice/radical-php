<?php
namespace Utility\Cache\Object;

interface ICache {
	function get($key);
	function set($key,$value,$ttl);
	function delete($key);
	function cachedValue($key_sem, $function, $ttl);
}