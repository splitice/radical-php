<?php
namespace Utility\Image\Upload;

use Basic\Structs\LoginDetails;
use Basic\Structs\UserPass;
use Basic\Structs\ApiKey;

abstract class _BASE {
	public $user;
	public $pass;
	public $key;
	
	const TYPE_SAFE = 1;
	const TYPE_ADULT = 2;
	
	function __construct(LoginDetails $details){
		if($details instanceof UserPass) {
			$this->user = $details->getUsername();
			$this->pass = $details->getPassword();
		}
		elseif ($details instanceof ApiKey)
			$this->key = $details->getKey;
	}
	
	static function Login(){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_TIMEOUT,30);
		return $ch;
	}
}
?>