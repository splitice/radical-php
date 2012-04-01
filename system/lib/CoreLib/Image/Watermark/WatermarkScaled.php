<?php
namespace Image\Watermark;

class WatermarkScaled extends Watermark {
	private $height;
	function __construct($src,$height = null) {
		parent::__construct($src);
		$this->height = $height;
	}
	private $maxHeight;
	function setMaxHeight($percent){
		$this->maxHeight = $percent;
	}
	private $maxWidth;
	function setMaxWidth($percent){
		$this->maxWidth = $percent;
	}
	function applyMark($to_watermark, $to_position){
		global $_CONFIG;
		//Get image sizes
		$img_size = getimagesize($to_watermark);
		$watermark_size = array(imagesx($this->watermark),imagesy($this->watermark));
		
		$height = $this->height;
		
		//$im = watermark
		$im = $this->watermark;
		
		if($height === null){
		
			//Estimate based on width
			$width = $img_size[0] * $this->maxWidth;
			$height = ceil($width/$watermark_size[0] * $watermark_size[1]);
			
			if($height > ($img_size[1] * $this->maxHeight)){
				//Do it with height as the primary
				$height = ceil($img_size[1] * $this->maxHeight);
				$width = ceil($this->maxHeight * $watermark_size[0]);//($height/$watermark_size[1])
			}
		}else{
			$width = floor($watermark_size[0] * ($height/$watermark_size[1]));
		}
		
		//Create image, disable alpha blending
		$tempimg = imagecreatetruecolor($width, $height);
		imagealphablending($tempimg, false);
		
		//Fill New image with transparent (transparent background)
		$transparent = imagecolorallocatealpha($tempimg, 255, 255, 255, 127);
		imagefilledrectangle($tempimg, 0, 0, $width, $height, $transparent);
		
		//Copy data from old image -> new
		imagecopyresampled($tempimg, $im, 0, 0, 0, 0, $width, $height, $watermark_size[0], $watermark_size[1]);
		
		//Set Watermark to newMark we created
		$this->watermark = $tempimg;
		
		//Do watermark
		$file = parent::applyMark($to_watermark, $to_position);
		
		//Destroy new watermark
		imagedestroy($tempimg);
		
		//Reset watermark
		$this->watermark = $im;
		
		return $file;
	}
}