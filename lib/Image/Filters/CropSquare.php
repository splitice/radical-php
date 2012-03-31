<?php
namespace Image\Filters;

class CropSquare extends Internal\FilterBase implements Interfaces\IExternalFilter {
	/**
	 * Output the data to be stored in URL
	 * @return array
	 */
	function toData(){
		return 0;
	}
	
	/**
	 * Do te work, this function is called on resource view
	 * @param resource $gd
	 * @param array $data
	 * @return resource
	 */
	static function Filter($gd,$data){		
		$width = imagesx($gd);
		$height = imagesy($gd);
		
		if($width > $height){
			$left = floor(($width - $height)/2);
			
			$gd = Crop::Filter($gd, array('x'=>$left,'y'=>0,'w'=>$height,'h'=>$height));	
		}elseif($width < $height){
			$top = floor(($height - $width)/2);

			$gd = Crop::Filter($gd, array('x'=>0,'y'=>$top,'w'=>$width,'h'=>$width));		
		}
		return $gd;
	}
}
