<?php
namespace Web\Page\Router\Recognisers;
use \Web\PageRecogniser\IPageRecognise;
use \Web\Pages;
use Web\Page\Handler;

class Special implements IPageRecognise {
	static function Recognise(\Net\URL $url){
		$url = $url->getPath();
		$path = $url->getPath(true);
		$ext = pathinfo($path,PATHINFO_EXTENSION);
		if($ext=='css'){
			if($url->firstPathElement() == 'css'){//Direct access dont combine
				$url->removeFirstPathElement();
				return new Pages\CSS_JS\CSS\Individual(array('name'=>(string)$path));
			}else{
				$name = substr($path,1,-4);
				return new Pages\CSS_JS\CSS\Combine(array('name'=>$name));
			}
		}
		if($ext=='js'){
			if($url->firstPathElement() == 'js'){//Direct access dont combine
				$url->removeFirstPathElement();
				return new Pages\CSS_JS\JS\Individual(array('name'=>(string)$path));
			}else{
				$name = substr($path,1,-3);
				return new Pages\CSS_JS\JS\Combine(array('name'=>$name));
			}
		}
	}
}