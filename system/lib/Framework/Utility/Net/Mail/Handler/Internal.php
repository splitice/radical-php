<?php
namespace Utility\Net\Mail\Handler;
use Utility\Net\Mail\Message;

class Internal implements IMailHandler {
	function send(Message $message,$body){
		$headers = '';
		
		// To send HTML mail, the Content-type header must be set
		if($message->getHtml()){
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' ;
		}
		
		$headers .= 'From: '.$message->getFrom();
		
		if($message->getReplyTo()){
			$headers .= "\r\n".'Reply-To: '.$message->getReplyTo();	
		}
		
		mail($message->getTo(),$message->getSubject(),$body,$headers);
	}
}