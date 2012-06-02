<?php
namespace Cast;

class Integer extends Internal\CastBase implements ICast {
	function Cast($value){
		//If is 64bit just return cast int
		if(PHP_INT_SIZE == 8){
			return (int)$value;
		}
		
		//32bit safety magic
		//Round down
		$value = floor($value);
		
		//If is in integer range
		if(abs($value) < PHP_INT_MAX){
			$value = (int)$value;//Make int
		}
		
		return $value;
	}
}