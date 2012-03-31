<?php
namespace Image\Filters;

class Resize extends Internal\FilterBase implements Interfaces\IExternalFilter {
	protected $width;
	protected $height;
	
	/**
	 * Create the object, store the data
	 * @param int $width
	 * @param int $height
	 */
	function __construct($width,$height){
		$this->width = $width;
		$this->height = $height;
	}
	/**
	 * Output the data to be stored in URL
	 * @return array
	 */
	function toData(){
		return array('w'=>$this->width,'h'=>$this->height);
	}
	
	/**
	 * Do te work, this function is called on resource view
	 * @param resource $gd
	 * @param array $data
	 * @return resource
	 */
	static function Filter($gd,$data){
		if(!isset($data['w']) || !isset($data['h']) || (!is_numeric($data['w']) && $data['w'] != 'auto') || (!is_numeric($data['h']) && $data['h'] != 'auto')){
			return $gd;
		}
		
		if($data['w'] == 'auto' && $data['h'] == 'auto'){
			return $gd;
		}
		
		$width = imagesx($gd);
		$height = imagesy($gd);
		
		if($data['w'] == 'auto'){
			$data['w'] = floor($width * $data['h']/$height);
		}elseif($data['h'] == 'auto'){
			$data['h'] = floor($height*$data['w']/$width);
		}
		
		if($data['w'] >= $width || $data['h'] >= $height){
			return $gd;
		}
		
		$gd2 = imagecreatetruecolor($data['w'], $data['h']);

		imagecopyresampled($gd2, $gd, 0, 0, 0, 0, $data['w'], $data['h'], $width, $height);
		
		return $gd2;
	}
}