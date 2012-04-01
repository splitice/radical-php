<?php
namespace Basic\DateTime;

use Core\StandardObject;

class Timestamp extends StandardObject {
	protected $timestamp;
	
	function __construct($timestamp){
		$this->timestamp = $timestamp;
	}
	function toFormat($format){
		return date($format,$this->timestamp);
	}
	function toAgo($ago=true)
	{
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");
	
		$now = time();
	
		$difference     = $now - $this->timestamp;
		$tense         = "ago";
	
		for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
			$difference /= $lengths[$j];
		}
	
		$difference = round($difference);
	
		if($difference != 1) {
			$periods[$j].= "s";
		}
	
		if($ago){
			$ago = ' ago';
		}
	
		return $difference.' '.$periods[$j].$ago;
	}
	function __toString(){
		return (string)$this->timestamp;
	}
}