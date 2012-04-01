<?php
include(__DIR__.'/test.base.php');

$releases = array ();
$releases['SexyCougars.11.11.09.Grace.Evangeline.XXX.720p.MP4-KTR'] = 'Sexy Cougars - Grace Evangelin';

foreach($releases as $k=>$r){
	$p = \DDL\TitleParse\Scene\Parse::ParseRelease($k,'xxx');
	if($p){
		$title = $p->TitleBuild();
		if($title != $r){
			echo "[".get_class($p)."] Failed ",$k," was '",$title,"'\r\n";
		}
	}else{
		echo "Type Failed ",$k,"\r\n";
	}
}
exit;