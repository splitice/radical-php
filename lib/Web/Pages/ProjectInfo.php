<?php
namespace Web\Pages;
use Web\PageHandler;

class ProjectInfo extends PageHandler\HTMLPageBase {	
	protected function getInfo($path){
		$ret = array('files'=>0,'lines'=>0);
		foreach(\Folder::getIterator($path) as $file){
			if(is_file($file)){
				$ret['files']++;
				$ret['lines'] += count(file($file));
			}
		}
		return $ret;
	}
	function GET(){
		foreach(\ClassLoader::getLibraries() as $libName=>$libPath){
			echo '<h2>'.$libName.'</h2>';
			echo '<p>';
			$info = $this->getInfo($libPath);
			echo '<b>Files: </b>'.$info['files'].'<br />';
			echo '<b>Lines: </b>'.$info['lines'].'<br />';
			echo '</p>';
		}
	}
}