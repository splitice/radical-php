<?php
namespace Video;

class Metadata {
	protected $file;
	protected $metadata = array();
	/**
	 * @var \Video\Metadata\IMetaData
	 */
	protected $filetype;
	
	function __construct($file){
		$this->file = $file;
		$ext = strtoupper(pathinfo($this->file,PATHINFO_EXTENSION));
		$class = '\\Video\\Metadata\\'.$ext;
		
		if(class_exists($class)){
			$this->filetype = new $class();
		}
	}
	
	function Add($k,$v){
		if($this->filetype->supports($k)){
			$this->metadata[$k] = $v;
			return true;
		}
		return false;
	}
	
	function Execute(){
		$cmd = 'ffmpeg -i '.escapeshellarg($this->file).' -vcodec copy -acodec copy -y -map_metadata 0:0';
		
		foreach($this->metadata as $k=>$m){
			$cmd .= ' -metadata '.escapeshellarg($k).'='.escapeshellarg($m);
		}
		
		$out = dirname($this->file).DIRECTORY_SEPARATOR.'temp_'.basename($this->file);
		if(file_exists($out)){
			unlink($out);
		}
		
		$cmd .= ' '.escapeshellarg($out);
		
		exec($cmd);
		die(var_dump($out));
		if(file_exists($out)){
			//Replace out with file
			unlink($this->file);
			rename($out, $this->file);
		}
	}
}