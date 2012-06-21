<?php
namespace Web\Page\Handler;
use Web\Page\Handler as PH;

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