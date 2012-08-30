<?php
namespace Utility\Image\Graph\pChart;

class Color {
	public $R;
	public $G;
	public $B;
	
	function __construct($R,$G,$B){
		$this->R = $R;
		$this->G = $G;
		$this->B = $B;
	}
	
	function toHex($hash = false){
		$hex = '';
		if($hash) $hex = "#";
		
		$hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
		$hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
		$hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);
		
		return $hex; // returns the hex value including the number sign (#)
	}
	
	function __toString(){
		return $this->toHex();
	}
	
	static function fromHex($color){
		if ($color[0] == '#')
			$color = substr($color, 1);
		
		if (strlen($color) == 6)
			list($r, $g, $b) = array($color[0].$color[1],
					$color[2].$color[3],
					$color[4].$color[5]);
		elseif (strlen($color) == 3)
			list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
		else
			return false;
		
		$r = hexdec($r); $g = hexdec($g); $b = hexdec($b);
		
		return new Color($r, $g, $b);
	}
}
