<?php
namespace Web\Page\Controller;

use Web\Page\Handler\HTMLPageBase;
use Web\Page\Handler;

class CacheManifest extends HTMLPageBase {	
	function getFiles($path){
		foreach(glob($path.'/*') as $v){
			if(is_dir($v)){
				switch(basename($v)){
					case 'images':
					case 'css':
					case 'js':
						foreach(\Folder::ListDir($v,true) as $file){
							$file = substr($file,strlen($path));
							$file = ltrim($file,'/');
							$files[] = $file;
						}
						break;
				}
			}
		}
		
		//Add combined files
		$files[] = ltrim(CSS_JS\CSS\Combine::Link('main'),'/');
		$files[] = ltrim(CSS_JS\JS\Combine::Link('main'),'/');
		
		return $files;
	}
	
	/**
	 * Handle GET request
	 *
	 * @throws \Exception
	 */
	function GET(){
		\Web\Page\Handler::$stack->top()->headers->Add('Content-Type','text/cache-manifest');
		echo "CACHE MANIFEST\r\n\r\n";
		
		echo "CACHE:\r\n";
		
		$path = \AutoLoader::$webDir;
		$files = $this->getFiles($path);
		
		foreach($files as $f){
			echo $f,"\r\n";
		}
		
		echo "\r\nNETWORK:\r\n\r\n*\r\n";
	}
}