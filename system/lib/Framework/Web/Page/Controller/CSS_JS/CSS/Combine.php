<?php
namespace Web\Page\Controller\CSS_JS\CSS;
use Web\Page\Controller\CSS_JS\Internal\CombineBase;

class Combine extends CombineBase {
	const EXTENSION = 'css';
	const MIME_TYPE = 'text/css';
	
	protected function getFiles($expr = '*'){
		$files = parent::getFiles();
		$path = $this->getPath();
		$p = strlen($path);
		foreach($files as $k=>$f){
			if(substr($f,-5) == '.scss' || substr($f,-5) == '.sass'){
				$fp = strpos($f,$path);
				$f = substr($f,$fp+$p+1);
				if(strpos($f,'/') || $f[0] == '_')
					unset($files[$k]);
			}
		}
		return $files;
	}
}