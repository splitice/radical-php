<?php
namespace Web\PageHandler;
use Web\PageHandler as PH;

abstract class PageRequestBase {
	const MAX_REQUEST_DEPTH = 20;
	
	/**
	 * Headers to output
	 * @var \Page\HeaderManager
	 */
	public $headers;
	
	protected $page;
	
	function __construct(IPage $page){
		$this->page = $page;
		$this->headers = new HeaderManager();
	}
	
	
	function Execute($method){
		//Add to PageHandler Stack
		PH::Push($this);
	
		//Setup output buffering
		ob_start();
	
		$depth = 0;
		while($this->page->can($method)){
			$depth++;
	
			$return = $this->page->$method();
			if($return){
				ob_clean();
				$this->page = $return;
			}else{
				break;
			}
				
			//Infinite loop?
			if($depth > static::MAX_REQUEST_DEPTH){
				PH::Pop();
				ob_end_flush();
				$this->headers->Clear();
				throw new Exceptions\PageHandlerException('Max request depth of '.static::MAX_REQUEST_DEPTH.' exceeded.');
			}
		}
	
		//Nothing was handled
		if(!$depth){
			PH::Pop();
			ob_end_flush();
			$this->headers->Clear();
			throw new Exceptions\PageHandlerException('Invalid or unknown method '.$method);
		}
	}
	
	static function fromURL(\Net\URL $url){
		return \Web\PageRecogniser\Recognise::fromURL($url);
	}
}