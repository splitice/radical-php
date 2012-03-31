<?php
namespace Web\PageRecogniser\Recognisers;
use \Web\PageRecogniser\IPageRecognise;

class ProjectInfo implements IPageRecognise {
	static function Recognise(\Net\URL $url){
		$path = $url->getPath();
		if($path->firstPathElement() == 'PROJECT_INFO'){
			$path->removeFirstPathElement();
			
			if($path->firstPathElement() == 'TEST'){
				return \Web\PageHandler::Objectify('UnitTest');
			}else{
				return \Web\PageHandler::Objectify('ProjectInfo');
			}
		}
	}
}