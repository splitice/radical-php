<?php
namespace HTTP\Curl;

class Exception extends \Exception {
	function __construct($message,\HTTP\Curl $curl){
		$message = ' [URL: '.$curl[CURLOPT_URL].']';
		parent::__construct($message);
	}
}