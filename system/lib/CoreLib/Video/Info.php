<?php
namespace Video;

class Info {
	function __construct($video){
		$this->video = $video;
		$this->info = static::Process($video);
	}
	
	static function Process($video){
		$cmd = 'ffmpeg -i '.escapeshellarg($video).' 2>&1';

		$output = array();
		exec($cmd,$output);
		
		$ret = array();
		
		foreach($output as $o){
			$m = array();
			if(preg_match('#Duration: ([0-9]+)\:([0-9]+)\:([0-9]+)\.([0-9]+)(?:.+)bitrate\: (?:(?:([0-9]+) kb\/s)|(.+))#',$o,$m)){
				$ret['duration']['hours'] = $m[1];
				$ret['duration']['minutes'] = $m[2];
				$ret['duration']['seconds'] = $m[3];
				$ret['duration']['ms'] = $m[4];
				
				if($m[5]){
					$ret['bitrate'] = $m[5].'kb/s';
				}else{
					$ret['bitrate'] = $m[6];
				}
			}elseif(preg_match('#Video\: (.+),#',$o,$m)){
				$ret['video']['codec'] = $m[1];
				if(preg_match('#([0-9]+)x([0-9]+)#', $o,$m)){
					$ret['video']['width'] = $m[1];
					$ret['video']['height'] = $m[2];
				}
			}elseif(preg_match('#Audio\: (.+), ([0-9]+) Hz, (.+)#',$o,$m)){
				$ret['audio']['codec'] = $m[1];
				$ret['audio']['freq'] = $m[2].'Hz';
			}
		}
		
		return $ret;
	}
	
	function getVideo(){
		return $this->info['video'];
	}
	function getAudio(){
		return $this->info['audio'];
	}
	function getDuration(){
		return $this->info['duration'];
	}
	function getBitrate(){
		return $this->info['bitrate'];
	}
}
?>