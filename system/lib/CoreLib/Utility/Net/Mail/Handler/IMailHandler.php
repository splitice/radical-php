<?php
namespace Utility\Net\Mail\Handler;
use Utility\Net\Mail\Message;

interface IMailHandler {
	function Send(Message $message,$body);
}