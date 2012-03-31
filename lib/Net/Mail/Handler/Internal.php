<?php
namespace Net\Mail\Handler;
use Net\Mail\Message;

class Internal implements IMailHandler {
	function Send(Message $message,$body){
		$headers = '';
		
		// To send HTML mail, the Content-type header must be set
		if($message->getHtml()){
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		}
		
		$headers .= 'From: '.$message->getFrom();
		
		mail($message->getTo(),$message->getSubject(),$body,$headers);
	}
}