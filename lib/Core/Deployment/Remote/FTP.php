<?php
namespace Core\Deployment\Remote;

use Basic\Structs\LoginDetails;

class FTP implements IRemoteLocation {
	protected $login;
	protected $host;
	private $basePath;
	private $context;
	
	function __construct(LoginDetails $login,$host,$basePath = '/'){
		$this->login = $login;
		$this->host = $host;
		$this->basePath = '/'.trim($basePath,'/').'/';
		$this->context = stream_context_create(array('ftp'=>array('overwrite'=>true)));
	}
	function toPath($file){
		return "ftp://".urlencode($this->login->getUsername()).":".urlencode($this->login->getPassword())."@".$this->host.$this->basePath.$file;
	}
	function writeFile($file,$data){
		$path = '';
		foreach(explode(DIRECTORY_SEPARATOR,dirname($file)) as $v){
			$path .= DIRECTORY_SEPARATOR.$v;
			$fp = $this->toPath($path);
			if(!file_exists($fp)){
				mkdir($fp);
			}
		}
		$path = $this->toPath($file);
		$handle = fopen($path, "wb", false, $this->context);
		if($handle){
			echo "Uploading ".$file."\r\n";
			//ftruncate($handle, 0);
			fwrite($handle,$data);
			fclose($handle);
		}else{
			echo "Failed Uploading ".$file."\r\n";
		}
	}
}