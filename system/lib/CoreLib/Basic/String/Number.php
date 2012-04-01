<?php
namespace Basic\String;

class Number extends \Core\Object {
	static function Ordinal($number){
		$ends = array('th','st','nd','rd','th','th','th','th','th','th');
		if (($number %100) >= 11 && ($number%100) <= 13)
			$abbreviation = $number. 'th';
		else
			$abbreviation = $number. $ends[$number % 10];
		
		return $abbreviation;
	}
	static function Natural($int, $plurals = false) {
		$readable = array ("", "thousand", "million", "billion" );
		$index = 0;
		while ( $int > 1000 ) {
			$int /= 1000;
			$index ++;
		}
		$s = '';
		$num = round ( $int, 0 );
		if ($num != 1 && $plurals) {
			$s = 's';
		}
		return ($num . " " . $readable [$index]);
	}
}