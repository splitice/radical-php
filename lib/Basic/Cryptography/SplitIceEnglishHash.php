<?php
namespace Basic\Cryptography;

use Basic\Cryptography\Internal\HashBase;

class SplitIceEnglishHash extends HashBase implements HashTypes\IOneWayHash {
	static $romans = array(
			'M' => 1000,
			'CM' => 900,
			'D' => 500,
			'CD' => 400,
			'C' => 100,
			'XC' => 90,
			'L' => 50,
			'XL' => 40,
			'X' => 10,
			'IX' => 9,
			'V' => 5,
			'IV' => 4,
			'I' => 1,
	);
	
	static $roman_cache = array();
	static function Hash($str){
		$string = '';
	
		//Pre Filter
		$str = preg_replace(array('#^\[[^\]]+\]#s','#(\s)\s+#s'), array('',' '), $str);
		$str = str_replace('&', ' and ', $str);
	
		//Remove non alphanumeric characters
		for($i = 0, $f = strlen ( $str ); $i < $f; $i ++) {
			if (ctype_alnum ( $str {$i} )) {
				$string .= $str {$i};
			}
		}
	
		$string = strtoupper($string);
	
		if(substr($str,0,3) === 'THE'){
			$str = substr($str,3);
		}
	
		//Handle Roman Numerals (will cause corruption, but its being hashed.... so meh)
		$class = __CLASS__;
		$string = preg_replace_callback('#(?:(?:XC|XL|L?X{1,3})(?:IX|IV|V?I{0,3}))|IX|IV|(?:V?I{1,3})#s', function($m) use($class){
			$roman = $m[0];
	
			if(isset(self::$roman_cache[$roman])){
				return (string)$class::$roman_cache[$roman];
			}
	
			$result = 0;
			$pos = 0;
	
			foreach ($class::$romans as $key => $value) {
				if(!isset($roman{$pos})){
					break;
				}
				$kl = strlen($key);
				while (isset($roman{$pos+$kl-1}) && 0 === substr_compare($roman, $key, $pos, $kl)) {
					$result += $value;
					$pos += $kl;
				}
			}
	
			$class::$roman_cache[$roman] = $result;
	
			return (string)$result;
		}, $string);
	
		return $string;
	}
}