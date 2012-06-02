<?php
namespace File;

class Size {
	private $bytes;
	function __construct($bytes){
		$this->bytes = $bytes;
	}
	function toHuman($max = null, $system = 'bi', $retstring = '%01.2f %s') {
		$size = $this->bytes;
		
		// Pick units
		$systems ['si'] ['prefix'] = array ('B', 'K', 'MB', 'GB', 'TB', 'PB' );
		$systems ['si'] ['size'] = 1000;
		$systems ['bi'] ['prefix'] = array ('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB' );
		$systems ['bi'] ['size'] = 1024;
		$sys = isset ( $systems [$system] ) ? $systems [$system] : $systems ['si'];
	
		// Max unit to display
		$depth = count ( $sys ['prefix'] ) - 1;
		if ($max && false !== $d = array_search ( $max, $sys ['prefix'] )) {
			$depth = $d;
		}
	
		// Loop
		$i = 0;
		while ( $size >= $sys ['size'] && $i < $depth ) {
			$size /= $sys ['size'];
			$i ++;
		}
	
		return sprintf ( $retstring, $size, $sys ['prefix'] [$i] );
	}
	static function fromHuman($str) {
		$str = trim($str);
	
		$bytes = 0;
	
		$bytes_array = array ('B' => 1, 'KB' => 1024, 'MB' => 1024 * 1024, 'GB' => 1024 * 1024 * 1024, 'TB' => 1024 * 1024 * 1024 * 1024, 'PB' => 1024 * 1024 * 1024 * 1024 * 1024 );
	
		$bytes = floatval ( $str );
	
		if (preg_match ( '#([KMGTP]?B)$#si', $str, $matches ) && ! empty ( $bytes_array [$matches [1]] )) {
			$bytes *= $bytes_array [$matches [1]];
		}
	
		$bytes = intval ( round ( $bytes, 2 ) );
	
		return new static($bytes);
	}
}