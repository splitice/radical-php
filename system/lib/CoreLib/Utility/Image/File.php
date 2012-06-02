<?php
namespace Image;

class File extends \File {
	const TYPE_SAFE = 1;
	const TYPE_ADULT = 2;
	
	function Watermark(Watermark\IWatermark $watermark, $pos){
		return $watermark->applyMark($this, $pos);
	}
	
	function Upload(Upload\IUploadHost $host,$type,$size='500x500'){
		return $host->Upload($this->file,$type,$size);		
	}
	
	function __toString(){
		return (string)$this->file;
	}
}