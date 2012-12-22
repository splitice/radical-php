<?php
namespace Utility\Net;

class DNSBL {
	const LIST_SPAMHAUS = 'sbl.spamhaus.org';
	const LIST_DSBL = 'list.dsbl.org';
	const LIST_DSBL = 'bl.spamcop.net';
	
	private $list;
	function __construct($list){
		$this->list = $list;
	}
	
	function blacklisted($ip){
		if(!($ip instanceof IP))
			$ip = new IP($ip);
		
		$ip = $ip->reverse();
		
		return checkdnsrr($ip . "." . $this->list . ".", "A");
	}
}