<?php
namespace Web\Page\Handler;
use Web\Page\Handler as PH;

class NullPageRequest extends PageRequestBase {	
	function Execute($method){
		//Flush Output
		$this->headers->Output();
		ob_end_flush();
	}
	
	function __destruct(){
		$this->Execute('GET');
		exit;
	}
}