<?php
namespace Web\Page\Cache;

use Web\Page\Handler\HeaderManager;

interface ICacheManager {
	/**
	 * Called after all the PageHandler itterations are done
	 * 
	 * @param HeaderManager $headers
	 */
	function postExecute(HeaderManager $headers);
}