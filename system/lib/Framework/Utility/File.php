<?php
namespace Utility;

/**
 * Static interface for file functions
 * 
 * @author SplitIce
 *
 */
class File {	
	/**
	 * Get the size of a file
	 * 
	 * @param string $file
	 * @return number
	 */
	static function size($file){
		$file = new File\Instance($file);
		return $file->Size();
	}
	
	/**
	 * Get a temporary file
	 * 
	 * @param string $filename
	 * @param string $data
	 * @return \Utility\File
	 */
	static function temporary($filename,$data = ''){
		$filename = '/tmp/'.$filename;
		file_put_contents($filename, $data);
		return new static($filename);
	}
}