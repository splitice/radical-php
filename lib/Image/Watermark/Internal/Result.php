<?php
namespace Image\Watermark\Internal;

class Result {
	function __construct($image){
		$this->image = $image;
	}
	
	function toFile($file) {
		$file = (string)$file;
		
		$width = imagesx($this->image);
		$height = imagesy($this->image);
			
		imagejpeg ( $this->image, $file, 90 );
		$quality = 80;
		while(filesize($file) > (1.5*1024*1024)){
			imagejpeg ( $this->image, $file, $quality );
			$quality--;
			if($quality<40){
				break;
			}
		}
		
		return new \Image\File($file);
	}
}