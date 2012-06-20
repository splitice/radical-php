<?php
namespace Web\Page\Handler;
use Web\Page\Handler as PH;

class PageRequest extends PageRequestBase {	
	function Execute($method){
		parent::Execute($method);
		
		//If people dont utilise the checks until now this will catch it at the end of the request
		if(isset($this->headers->headers['Last-Modified'])){
			$lmts = strtotime($this->headers->headers['Last-Modified']);
			if($ims = \Web\Page\Request::header('If-Modified-Since')){
				if($lmts <= strtotime($ims)){
					while(ob_get_level()) ob_end_clean();
					ob_start();
					$this->headers->Status(304);
					$flush = false;
				}
			}
		}
		
		//Flush Output
		$this->headers->Output();
		ob_end_flush();
		
		//Remove from stack
		PH::Pop();
	}
}