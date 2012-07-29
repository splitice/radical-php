<?php
namespace Web\Page\Admin\Modules;
use Web\Page\Admin\MultiAdminModuleBase;
use Web\Page\Handler;

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
		return $this->index();
	}
	function index(){
		$libs = array();
		foreach(\Core\Libraries::getLibraries() as $libName=>$libPath){
			$libs[$libName] = $this->getInfo($libPath);
		}
		
		return $this->_T('ProjectInfo/info',array('libraries'=>$libs));
	}
	function actionUnitTest(){
		$testResults = \Core\Debug\Test\Controller::RunUnitTests();
	
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