<?php
include(__DIR__.'/test.base.php');

$releases = array ();
$releases['Beethovens.Christmas.Adventure.2011.DVDRip.XVID.AC3.HQ.Hive-CM8'] = 'Beethovens Christmas Adventure (2011) DVDRip';
$releases['Alyce.2011.DVDRip.XVID.AC3-5.1.HQ.Hive-CM8'] = 'Alyce (2011) DVDRip';
$releases['Lisa.Lampanelli.The.Queen.of.Mean.2002.DVDRip.XviD-FiCO'] = 'Lisa Lampanelli The Queen of Mean (2002) DVDRip';

foreach($releases as $k=>$r){
	$p = \DDL\TitleParse\Scene\Parse::ParseRelease($k,'movie');
	if($p){
		if($p->BuildTitle() != $r){
			echo "Failed ",$k,"\r\n";
		}
	}else{
		echo "Type Failed ",$k,"\r\n";
	}
}

exit;