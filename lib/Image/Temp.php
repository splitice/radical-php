<?php
namespace Image;

class Temp extends \Image\File {
	static function getTempName(){
		return tempnam('/tmp/',md5(get_called_class()));
	}
	function __construct($file){
		while(is_resource($file) || (is_object($file) && $file instanceof Interfaces\IFetch)){
			if(is_resource($file)){
				$type = get_resource_type($file);
				switch($type){
					case 'curl':
						$data = curl_exec($file);
						file_put_contents($file = static::getTempName(), $data);
						break;
					case 'gd':
						imagejpeg($file, $file = static::getTempName().'.jpeg', 90);
						break;
					default:
						throw new \Exception('Unknown resource type: '.$type);
				}
			}else{
				$data = $file->Fetch();
				file_put_contents($file = static::getTempName(), $data);
			}
		}
		
		parent::__construct($file);
	}
	function __destruct(){
		@unlink($this->file);
	}
}