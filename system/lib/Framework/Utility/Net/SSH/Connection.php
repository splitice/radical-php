<?php
namespace Utility\Net\SSH;

class Connection {
	//Connection Details
	private $host;
	private $port;
	
	//SSH Connection
	protected $ssh;
	
	//SFTP
	private $sftp;
	
	//Authentication SubClass
	public $authenticate;
	
	function __construct($host,$port,$methods = array()){
		//Store details
		$this->host = $host;
		$this->port = $port;
		
		//Setup Auth
		$this->authenticate = new Authenticate(&$this->ssh);
		
		//Connect
		$this->Connect($methods);
	}
	
	function Connect($methods = array()){
		//Connect Resource
		$this->ssh = ssh2_connect($this->host,$this->port,$methods,array('disconnect'=>array($this,'onDisconnect')));
		
		//Re-connection authentication
		$this->authenticate->ssh = $this->ssh;
		$this->authenticate->Authenticate($this->authenticate);
		
		if($this->sftp){
			$this->sftp->Init($this);
		}
	}
	
	function Close(){
		if($this->ssh !== null){
			$this->exec('exit');
			$this->ssh = null;
		}
	}
	function __destruct(){
		try {
			$this->Close();
		}catch(\Exception $ex){
			//I will except live with it
		}
	}
	
	function getResource(){
		return $this->ssh;
	}
	
	function isConnected(){
		return is_resource($this->ssh);
	}
	
	function Execute($command, $pty = null, array $env = array(), $width = 80, $height = 25, $width_height_type = SSH2_TERM_UNIT_CHARS){
		$stream = ssh2_exec($this->ssh,$command,$env,$width,$height,$width_height_type);
		
		if(false === $stream){
			throw new \Exception('Couldnt execute command, no stream returned');
		}
		
		stream_set_blocking($stream, true);
		
		return stream_get_contents($stream);
	}
	
	function Exec($command){
		return $this->Execute($command);
	}
	
	/**
	 * @return \Utility\Net\SSH\SFTP
	 */
	function SFTP(){
		if(!$this->sftp){
			//Store SFTP channel
			$this->sftp = new SFTP($this);
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