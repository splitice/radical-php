<?php
class File {
	function __construct($file){
		throw new \Exception('Static now, depreciated interface');
	}	
	
	static function Size($file){
		$file = new File\Instance($file);
		return $file->Size();
	}
	
	static function Temporary($filename,$data = ''){
		$filename = '/tmp/'.$filename;
		file_put_contents($filename, $data);
		return new static($filename);
	}
}