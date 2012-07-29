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
	
		return $this->_T('ProjectInfo/unit',array('test_results'=>$libs));
	}
}