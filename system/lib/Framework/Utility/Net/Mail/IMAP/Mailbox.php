<?php
namespace Utility\Net\Mail\IMAP;

use Utility\Net\Mail\IMAP;
class Mailbox {
	private $con;
	private $mailbox;
	
	function __construct(IMAP $con, $mailbox){
		$this->con = $con;
		$this->mailbox = $mailbox;
	}
	
	function status(){
		return imap_status($this->con->con(), $this->mailbox, SA_ALL);
	}
}