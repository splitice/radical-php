<?php
namespace File;

class Instance {
	protected $file;
	
	function __construct($file){
		$this->file = $file;
	}
	function getStream(){
		return new Stream($this->file);
	}
	
	/**
	 * Use variables to shorten the path to file.
	 * @param array $vars
	 * @return string
	 */
	function Compact($vars){
		//Setup
		$path = $this->file;
		$pLen = strlen($path);
	
		//Compare
		foreach($vars as $k=>$v){
			$vLen = strlen($v);
			if(substr($path,0,$vLen) == $v){
				return '${'.$k.'}'.substr($path,$vLen);
			}
		}
	
		//Oh no, we couldnt do it!
		return $path;
	}
	
	function Exists(){
		return file_exists($this->file);
	}
	function Contents(){
		return file_get_contents($this->file);
	}
	function Rename($to) {
		rename ( $this->file, $to );
		$this->file = $to;
	}
	static function SantizeName($str) {
		$str = str_replace ( array ('/', '\\', '\'', '"' ), array (' ', ' ', '', '' ), $str );
		return $str;
	}
	static function NiceName($str) {
		$str = self::SantizeName ( $str );
		$str = preg_replace ( '#([^(a-zA-Z0-9\-\.)])#', ' ', $str );
		$str = preg_replace ( '#\s+#', ' ', $str );
		$str = str_replace ( ' ', '_', $str );
		return $str;
	}
	function Size() {
		if(!file_exists($this->file)){
			return null;
		}
		if(PHP_INT_SIZE == 8){
			return filesize($this->file);
		}
	
		// filesize will only return the lower 32 bits of
		// the file's size! Make it unsigned.
		$fmod = filesize ( $this->file );
		if ($fmod < 0)
			$fmod += 2.0 * (PHP_INT_MAX + 1);
	
		// find the upper 32 bits
		$i = 0;
	
		$myfile = fopen ( $this->file, "r" );
	
		// feof has undefined behaviour for big files.
		// after we hit the eof with fseek,
		// fread may not be able to detect the eof,
		// but it also can't read bytes, so use it as an
		// indicator.
		while ( strlen ( fread ( $myfile, 1 ) ) === 1 ) {
			fseek ( $myfile, PHP_INT_MAX, SEEK_CUR );
			$i ++;
		}
	
		fclose ( $myfile );
	
		// $i is a multiplier for PHP_INT_MAX byte blocks.
		// return to the last multiple of 4, as filesize has modulo of 4 GB (lower 32 bits)
		if (($i % 2) == 1)
			$i --;
		else
			$i-=2;
	
		//$i--;
			
		// add the lower 32 bit to our PHP_INT_MAX multiplier
		return (( float ) ($i) * (PHP_INT_MAX + 1)) + $fmod;
	}
	function __toString(){
		return $this->file;
	}
	function Delete(){
		unlink($this->file);
	}
}