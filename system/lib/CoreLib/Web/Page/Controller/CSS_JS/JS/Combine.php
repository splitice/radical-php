<?php
namespace Web\Pages\CSS_JS\JS;
use Web\Pages\CSS_JS\Internal\CombineBase;

class Combine extends CombineBase {
	const EXTENSION = 'js';
	const MIME_TYPE = 'text/javascript';
	
	function Optimize($code){
		return $code;
		return \HTML\Optimiser\Javascript\JSMin::minify($ret);
	}
}