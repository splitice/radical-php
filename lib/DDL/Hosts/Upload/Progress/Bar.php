<?php
namespace DDL\Hosts\Upload\Progress;
use CLI\Console\Progress\Bar as CLI_Bar;

class Bar extends CLI_Bar {
	function ProgressFunction($download_size, $downloaded, $upload_size, $uploaded){
		//Calculate Decimal Percent
		$percent = 0;
		if($download_size > $upload_size){
			if($download_size != 0){
				$percent = $downloaded/$download_size;
			}
		}else{
			if($upload_size != 0){
				$percent = $uploaded/$upload_size;
			}
		}
		
		//Calculate Percentage 0-100
		$percent *= 100;
		
		//Set Progress
		$this->setProgress($percent);
	}
	
	static function Callback(){
		return array(new static(),'ProgressFunction');
	}
}