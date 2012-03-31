<?php
namespace Image\Filters;

class RoundedCorners extends Internal\FilterBase implements Interfaces\IExternalFilter {
	protected $radius;
	
	/**
	 * Create the object, store the data
	 * @param int $radius
	 */
	function __construct($radius){
		$this->radius = $radius;
	}
	/**
	 * Output the data to be stored in URL
	 * @return array
	 */
	function toData(){
		return $this->radius;
	}
	
	/**
	 * Do the work, this function is called on resource view
	 * @param resource $gd
	 * @param array $data
	 * @return resource
	 */
	static function Filter($gd,$data){
		if(!is_numeric($data)){
			return $gd;
		}
		
		$width = imagesx($gd);
		$height = imagesy($gd);
		
		/* TODO */
		
		return $gd2;
	}
}