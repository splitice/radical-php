<?php
namespace CLI\Console;

class Details extends \Core\Object {
	private static $sizeCache;
	static function getSize(){
		if(self::$sizeCache){
			return self::$sizeCache;
		}
		
		preg_match("/rows.([0-9]+);.columns.([0-9]+);/", strtolower(exec('stty -a |grep columns')), $output);
		self::$sizeCache = array('width'=>$output[2],'height'=>$output[1]);
		return self::$sizeCache;
	}
	static function getWidth(){
		$size = self::getSize();
		return $size['width'];
	}
	static function getHeight(){
		$size = self::getSize();
		return $size['height'];
	}
}