<?php
namespace Net\ExternalInterfaces\SSH;

class Connection {
	//Connection Details
	private $host;
	private $port;
	
	//SSH Connection
	private $ssh;
	
	//SFTP
	private $sftp;
	public $inSFTP;
	
	//Authentication SubClass
	public $authenticate;
	
	function __construct($host,$port){
		//Store details
		$this->host = $host;
		$this->port = $port;
		
		//Connect Resource
		$this->ssh = ssh2_connect($host,$port,array(),array('disconnect'=>array($this,'onDisconnect')));
		
		//Setup Auth
		$this->authenticate = new Authenticate($this->ssh);
	}
	
	function Close(){
		$this->exec($this->ssh, 'exit');
		$this->ssh = null;
	}
	function __destruct(){
		$this->Close();
	}
	
	function getResource(){
		return $this->ssh;
	}
	
	function isConnected(){
		return is_resource($this->ssh);
	}
	
	function Execute($command, $pty = null, array $env = array(), $width = 80, $height = 25, $width_height_type = SSH2_TERM_UNIT_CHARS){
		if($this->inSFTP){
			throw new Exceptions\SFTPInProgressException('can not execute command');
		}
		$stream = ssh2_exec($this->ssh,$command,$env,$width,$height,$width_height_type);
		
		stream_set_blocking($stream, true);
		
		return stream_get_contents($stream);
	}
	
	function Exec($command){
		return $this->Execute($command);
	}
	
	function SFTP(){
		if(!$this->sftp){
			//Make new Connection
			$ssh = new static($this->host,$this->port);
			$ssh->authenticate->Authenticate($this->authenticate);
			
			//Allocate it to SFTP
			$this->sftp = new SFTP($ssh);
		}
		return $this->sftp;
	}
	
	function onDisconnect(){
		$this->ssh = null;
	}
	
	static function fromArray(array $in){
		return new static($in['host'],$in['port']);
	}
}