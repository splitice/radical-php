<?php
namespace Basic\String;

class Size extends \Core\Object {
	function __construct($string,$font){
		$this->string = $string;
		$this->font = $font;
	}
	
	function getSize(){
		$d = imagettfbbox(10,0,$this->font,$this->string);
		return array('width'=>$d[2]-$d[0],'height'=>$d[3]-$d[1]);
	}
	function getWidth(){
		$r = $this->getSize();
		return $r['width'];
	}
	
	function getCenteredLeft($width){
		$half = $width/2;
		$half -= $this->getWidth()/2;
		return floor($half);
	}
}