<?php
namespace Basic\DateTime;

class Timespan {
	protected $seconds;
	
	function __construct($seconds){
		$this->seconds = $seconds;
	}
	
	static function fromBetween($timestamp1,$timestamp2){
		return new static(abs($timestamp1 - $timestamp2));
	}
	static function fromHuman($d){
		if(($a=substr($d,-4))=='hour' && is_numeric($b = trim(substr($d,0,-4)))){
			return ($b*60*60);
		}elseif(($a=substr($d,-5))=='hours' && is_numeric($b = trim(substr($d,0,-5)))){
			return ($b*60*60);
		}elseif(($a=substr($d,-3))=='min' && is_numeric($b = trim(substr($d,0,-3)))){
			return ($b*60);
		}elseif(($a=substr($d,-4))=='mins' && is_numeric($b = trim(substr($d,0,-4)))){
			return ($b*60);
		}
		return new static($d);
	}
}