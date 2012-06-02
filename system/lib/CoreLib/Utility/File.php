<?php
namespace Utility;

class File extends File\Instance {	
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