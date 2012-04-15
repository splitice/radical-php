<?php
namespace Basic\DateTime;

class DateTime extends DateTimeShared {
	const DATABASE_FORMAT = "Y-m-d H:i:s";
	
	function getMeridiem(){
		return $this->toFormat('a');
	}
	function isAM(){
		return ($this->getMeridiem() == 'am');
	}
	function isPM(){
		return ($this->getMeridiem() == 'pm');
	}
	function getHour($twentyfourhour = true){
		if($twentyfourhour){
			return (int)$this->toFormat('G');
		}else{
			return (int)$this->toFormat('g');
		}
	}
	function getMinute(){
		return (int)$this->toFormat('i');
	}
	function getSecond(){
		return (int)$this->toFormat('s');
	}
	
	static function fromRaw($year,$month = null,$day = null, $hour = null, $minute = null, $second = null){
		return new static(mktime($hour,$minute,$second,$month,$day,$year));
	}
}