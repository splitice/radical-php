<?php
namespace Web\Page\Cache;

use Web\Page\Handler\HeaderManager;

/**
 * A smart cache manager, will implement caching in your script without
 * any input from you, although will also work better if you do.
 * 
 * @author SplitIce
 */
class DefaultCacheManager implements ICacheManager {
	function postExecute(HeaderManager $headers){
		//If people dont utilise the checks until now this will catch it at the end of the request
		if($headers->status == 200){
			if(isset($headers['Last-Modified'])){				
				//Check if the user sent a If-Modified-Since header in their request
				if($ims = \Web\Page\Request::header('If-Modified-Since')){
					//Parse the time in the last modified sent from the page handler
					$lmts = strtotime($headers['Last-Modified']);
					
					//If the user has an unmodified version (aparently) based on last modifed dates
					if($lmts <= strtotime($ims)){
						//Clear output buffers
						while(ob_get_level()) ob_end_clean();
						
						//Start a new buffer
						ob_start();
						
						//Send 304 not modified
						$this->headers->Status(304);
					}
				}
			}else{ //If no Last-Modified specified
			//Generate automatic etag from output
			
			//Look for etag in request
			}
		}
	}
}