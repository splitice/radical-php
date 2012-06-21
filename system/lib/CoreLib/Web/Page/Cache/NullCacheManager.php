<?php
namespace Web\Page\Cache;

/**
 * A cache manager to do nothing
 * 
 * Used in sub requests and for people who dont ever want caching
 * 
 * @author SplitIce
 */
class NullCacheManager implements ICacheManager {
	function postExecute(){
		//Do nothing
	}
}