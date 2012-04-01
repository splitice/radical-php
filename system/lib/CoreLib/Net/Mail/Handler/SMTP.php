<?php
namespace Net\Mail\Handler;
use Net\Mail\Message;

class SMTP extends Internal {
	private $host;
	private $port;
	
	function __construct($host=null,$port=21){
		if($host === null){
			global $_MAIL;
			$host = $_MAIL['host'];
		}
		$this->host = $host;
		$this->port = $port;
	}
	function Send(Message $message,$body){
		//Read INI
		$smtp = ini_get("SMTP");
		$port = ini_get("smtp_port");
		
		//Set INI
		ini_set("SMTP", $this->host);
		ini_set("smtp_port", $this->port);
		
		//Send mail
		parent::Send($message, $body);
		
		//reset INI
		ini_set("SMTP", $smtp);
		ini_set("smtp_port", $port);
	}
}