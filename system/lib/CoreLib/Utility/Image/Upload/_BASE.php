<?php
namespace Image\Upload;

use Basic\Structs\LoginDetails;

abstract class _BASE {
	public $user;
	public $pass;
	
	const TYPE_SAFE = 1;
	const TYPE_ADULT = 2;
	
	function __construct(LoginDetails $details){
		$this->user = $details->getUsername();
		$this->pass = $details->getPassword();
	}
	
	static function Login(){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_TIMEOUT,30);
		return $ch;
	}
}
?>