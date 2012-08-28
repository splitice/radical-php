<?php
namespace Utility\Net\Mail\Handler;
use Utility\Net\Mail\Message;

interface IMailHandler {
	function send(Message $message,$body);
}