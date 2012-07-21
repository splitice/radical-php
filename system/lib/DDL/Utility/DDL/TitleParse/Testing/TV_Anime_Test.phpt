<?php
include(__DIR__.'/test.base.php');

$releases = array ();
$releases['[Coalgirls] Cardcaptor Sakura SP (960x720 Blu-ray FLAC)[A427519B].mkv'] = 'Cardcaptor Sakura SP (720p)';
$releases['[SG] Hitman Reborn 197 [720p] [75D8F5D1].mkv'] = 'Hitman Reborn - 197 (720p)';
$releases['[Licca] The Alps Story - My Annette Episode 01 (XviD) [AC15A841].avi'] = 'The Alps Story: My Annette - 1';
$releases['[sage] Mobile Suit Gundam AGE - 05v2 [720p][10bit][A646DCB1].mkv'] = 'Mobile Suit Gundam AGE - 5 (720p)';
$releases['[UTW] Carnival Phantasm - 06 [BD][XviD][DA55024F].avi'] = 'Carnival Phantasm - 6';
$releases['[Shikkaku] Ben-To 05 [10bit][1280x720][AAC][A37B4167].mkv'] = 'Ben-To - 5 (720p)';
$releases['[DmonHiro] A+Channel 01 (BD, 720, 10bit, AAC).mkv'] = 'A+Channel - 1 (720p)';
$releases['Darker Than Black II - OVA 4 [x264 854x480 AC3 DVD][Dual Audio][Sakura][C-W].mkv'] = 'Darker Than Black II - OVA 4 (480p)';
$releases['eX-Driver -The Movie- [x264 848x480 AC3 DVD][Dual Audio][Sakura][C-W][961CA240].mkv'] = null;

foreach($releases as $k=>$r){
	$p = \DDL\TitleParse\Scene\Parse::ParseRelease($k,'tv');
	if($p){
		if($p->BuildTitle() != $r){
			echo "Failed ",$k," with ",$p->BuildTitle(),"\r\n";
		}
	}else{
		if($p != $r){
			echo "Type Failed ",$k,"\r\n";
		}
	}
}

exit;