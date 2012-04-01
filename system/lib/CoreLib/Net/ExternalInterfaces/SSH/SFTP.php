<?php
namespace Net\ExternalInterfaces\SSH;

class SFTP {
	/**
	 * @var \Net\ExternalInterfaces\SSH\Connection
	 */
	private $ssh;
	
	private $sftp;
	
	function __construct(Connection $ssh){
		$ssh->inSFTP = true;
		$this->ssh = $ssh;
		$this->sftp = ssh2_sftp($ssh->getResource());
	}
	
	function getFile($path){
		$file = $this->newFile($path);
		if(!$file->Exists()){
			throw new \Exceptions\FileNotExists($path);
		}
		return $file;
	}
	function newFile($path){
		return new \File\Instance($this->getPath($path));
	}
	function getPath($path){
		return "ssh2.sftp://${sftp}${path}";
	}
	
	function Close(){
		$this->ssh->inSFTP = false;
	}
	
	function __destruct(){
		$this->Close();
	}
	
	function __toString(){
		return (string)$this->sftp;
	}
}