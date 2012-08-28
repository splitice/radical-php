<?php
namespace Web\Page\Controller\CSS_JS\JS;

use Web\Page\Controller\CSS_JS\Internal\CombineBase;
use Web\Optimiser;

class Combine extends CombineBase {
	const EXTENSION = 'js';
	const MIME_TYPE = 'text/javascript';
	
	function optimize($code){
		return $code;
		return Optimiser\Javascript\JSMin::minify($ret);
	}
}