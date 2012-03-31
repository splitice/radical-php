<?php
namespace Web\Pages;
use Web\PageHandler;

class UnitTest extends PageHandler\HTMLPageBase {	
	function GET(){
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
		
		exit;
	}
}