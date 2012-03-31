<?php
namespace Web\PageRecogniser\Recognisers;
use \Web\PageRecogniser\IPageRecognise;
use \Web\Pages;
use Web\PageHandler;

class Special implements IPageRecognise {
	static function Recognise(\Net\URL $url){
		$url = $url->getPath();
		$ext = pathinfo($url,PATHINFO_EXTENSION);
		if($ext=='css'){
			if($url->firstPathElement() == 'css'){//Direct access dont combine
				$url->removeFirstPathElement();
				return new Pages\CSS_JS\CSS\Individual(array('name'=>(string)$url));
			}else{
				$name = substr($url,1,-4);
				return new Pages\CSS_JS\CSS\Combine(array('name'=>$name));
			}
		}
		if($ext=='js'){
			if($url->firstPathElement() == 'js'){//Direct access dont combine
				$url->removeFirstPathElement();
				return new Pages\CSS_JS\JS\Individual(array('name'=>(string)$url));
			}else{
				$name = substr($url,1,-3);
				return new Pages\CSS_JS\JS\Combine(array('name'=>$name));
			}
		}
	}
}