<?php
namespace Utility\Net\SSH;

class SFTP {
	const SCHEME = 'ssh2.sftp://';
	/**
	 * @var \Utility\Net\SSH\Connection
	 */
	private $ssh;
	
	private $sftp;
	
	function __construct(Connection $ssh){
		echo "SFTP CONSTRUCT\r\n";
		$this->ssh = $ssh;
		$this->Init($ssh);
	}
	
	function init(Connection $ssh){
		echo "SFTP INIT\r\n";
		$this->ssh = $ssh;
		$this->sftp = ssh2_sftp($ssh->getResource());
	}
	
	function getSftp(){
		return $this->sftp;
	}
	
	function getFile($path,$mustExist = false){
		$file = $this->newFile($path);
		if($mustExist && !$file->Exists()){
			throw new \Exceptions\FileNotExists($path);
		}
		return $file;
	}
	function getConnection(){
		return $this->ssh;
	}
	function newFile($path){
		return new \File($this->getPath($path));
	}
	function getPath($path){
		return self::SCHEME.$this->sftp.$path;
	}
	function getLocalPath($path){
		if(substr($path,0,strlen(self::SCHEME)) == self::SCHEME){
			if(preg_match('`Resource id #(?:[0-9]+)(.+)`', $path, $m)){
				return $m[1];
			}
		}
		return $path;
	}
	
	function stat($filename){
		$filename = $this->getLocalPath($filename);
		return ssh2_sftp_stat ($this->sftp,$filename);
	}
	
	function mkdir($filename){
		$filename = $this->getPath($filename);
		return mkdir ($filename);
	}
	
	function ctime($filename){
		$filename = $this->getLocalPath($filename);
		$cmd = 'stat -c %Z '.escapeshellarg($filename);
		$ret = trim($this->ssh->Exec($cmd));
		return (int)$ret;
	}
	
	function __toString(){
		return (string)$this->sftp;
	}
}