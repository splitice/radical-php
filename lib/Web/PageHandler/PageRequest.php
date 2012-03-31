<?php
namespace Web\PageHandler;
use Web\PageHandler as PH;

class PageRequest extends PageRequestBase {	
	function Execute($method){
		parent::Execute($method);
		
		//Flush Output
		$this->headers->Output();
		ob_end_flush();
		
		//Remove from stack
		PH::Pop();
	}
}