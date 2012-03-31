<?php
namespace Image\Filters;

class ImageJoin extends Internal\FilterBase implements Interfaces\IInternalFilter {
	protected $type;
	protected $image;
	
	/**
	 * Create the object, store the data
	 * @param string $radius
	 * @param resource $secondImage
	 */
	function __construct($type,$image){
		$this->type = $type;
		$this->image = $image;
	}
	
	function Process($gd){
		if(!is_string($this->type)){
			return null;
		}
		
		$this->type = strtolower($this->type);
		
		//Either horizontal or vertical merge
		if($this->type != 'horizontal' && $this->type != 'vertical'){
			return null;
		}
		
		//Image 1
		$width1 = imagesx($this->image);
		$height1 = imagesy($this->image);
		
		//Image 2
		$width2 = imagesx($gd);
		$height2 = imagesy($gd);
		
		//Process
		switch($this->type){
			case 'horizontal':
				$imc = imagecreatetruecolor($width1 + $width2, $height);
				
				//Copy Image
				imagecopy($imc,$im1,0,0,0,0,$width1,$height);
				imagecopy($imc,$im2,$width1,0,0,0,$width2,$height);
				break;
				
			case 'vertical':
				break;
		}
		
		//Destroy
		imagedestroy($gd);
		imagedestroy($this->image);
		
		return $imc;
	}
}