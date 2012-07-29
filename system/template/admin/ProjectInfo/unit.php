<?php
foreach($_->vars['test_results'] as $class=>$v){
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