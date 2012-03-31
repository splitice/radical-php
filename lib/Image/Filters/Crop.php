<?php
namespace Image\Filters;

class Crop extends Internal\FilterBase implements Interfaces\IExternalFilter {
	protected $x;
	protected $y;
	protected $width;
	protected $height;
	
	/**
	 * Create the object, store the data
	 * @param int $width
	 * @param int $height
	 */
	function __construct($x,$y,$width,$height){
		$this->x = $x;
		$this->y = $y;
		$this->width = $width;
		$this->height = $height;
	}
	/**
	 * Output the data to be stored in URL
	 * @return array
	 */
	function toData(){
		return array('x'=>$this->x,'y'=>$this->y,'w'=>$this->width,'h'=>$this->height);
	}
	
	/**
	 * Do te work, this function is called on resource view
	 * @param resource $gd
	 * @param array $data
	 * @return resource
	 */
	static function Filter($gd,$data){
		if(!isset($data['x']) || !isset($data['y']) || !isset($data['w']) || !isset($data['h']) || 
			!is_numeric($data['x']) || !is_numeric($data['y']) || !is_numeric($data['w']) || !is_numeric($data['h'])){
			return $gd;
		}
		
		$width = imagesx($gd);
		$height = imagesy($gd);

		
		$gd2 = imagecreatetruecolor($data['w'], $data['h']);
		
		imagecopy($gd2, $gd, 0, 0, $data['x'], $data['y'], $data['w'], $data['h']);
		
		return $gd2;
	}
}