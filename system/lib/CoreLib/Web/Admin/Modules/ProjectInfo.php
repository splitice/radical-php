<?php
namespace Web\Admin\Modules;
use Web\Admin\MultiAdminModuleBase;
use Web\PageHandler;

class ProjectInfo extends MultiAdminModuleBase {	
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
	function actionInfo(){
		foreach(\ClassLoader::getLibraries() as $libName=>$libPath){
			echo '<h2>'.$libName.'</h2>';
			echo '<p>';
			$info = $this->getInfo($libPath);
			echo '<b>Files: </b>'.$info['files'].'<br />';
			echo '<b>Lines: </b>'.$info['lines'].'<br />';
			echo '</p>';
		}
	}
	function actionUnitTest(){
		$testResults = \Debug\Test\Controller::RunUnitTests();
	
		foreach($testResults as $class=>$v){
			echo '<h1>',$class,'</h1>';
			foreach($v as $method=>$vv){
				echo '<h2>',$method,'</h2>';
				foreach($vv as $test){
					if($test['result'] == 'exception'){
	
					}else{
						echo $test['message'], ': <b>', $test['result'],'</b><br />';
					}
				}
			}
		}
	}
}