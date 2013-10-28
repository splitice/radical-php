<?php
namespace Web\Page\Handler;

use Web\Page\Cache\DefaultCacheManager;
use Web\Page\Handler\Exceptions\PageHandlerException;
use Utility\Net\External\ContentAPI\Recognise;
use Web\Page\Handler as PH;

abstract class PageRequestBase {
	const MAX_REQUEST_DEPTH = 20;
	
	/**
	 * Headers to output
	 * @var \Web\Page\Handler\HeaderManager
	 */
	public $headers;
	
	protected $page;
	protected $cache;
	
	function __construct(IPage $page = null){
		$this->page = $page;
		$this->headers = new HeaderManager();
		$this->cache = new DefaultCacheManager();
	}
	
	private function can_fake($method){
		return $method == 'HEAD';
	}
	
	function execute($method){
		//No assumptions
		$method = strtoupper($method);
		
		//Add to Page\Handler Stack
		PH::Push($this);
	
		//Setup output buffering
		ob_start();
	
		$depth = 0;
		while($this->page->can($method) || $this->can_fake($method)){
			$depth++;
	
			$return = $this->page->$method();
			if($return){
				ob_clean();
				$this->page = $return;
			}else{
				if(!$this->page->can($method)){
					if($method == 'HEAD'){
						//$contents = ob_get_contents();
						//Headers only, no body
						ob_clean();
					}
				}
				break;
			}
				
			//Infinite loop?
			if($depth > static::MAX_REQUEST_DEPTH){
				PH::Pop();
				ob_end_flush();
				$this->headers->Clear();
				throw new PageHandlerException('Max request depth of '.static::MAX_REQUEST_DEPTH.' exceeded.');
			}
		}
	
		//Nothing was handled
		if(!$depth){
			PH::Pop();
			ob_end_flush();
			$this->headers->Clear();
			throw new PageHandlerException('Invalid or unknown method '.$method);
		}
		
		//Pass to the cache handler
		if($method=='GET') $this->cache->postExecute($this->headers);
	}
	
	static function fromURL(\Utility\Net\URL $url){
		return Recognise::fromURL($url);
	}
}