<?php
namespace Net\Mail\Handler;
use Net\Mail\Message;

interface IMailHandler {
	function Send(Message $message,$body);
}