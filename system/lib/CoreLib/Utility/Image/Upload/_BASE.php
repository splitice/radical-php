<?php
namespace Utility\Image\Upload;

use Basic\Structs\UserPass;
use Basic\Structs\ApiKey;


abstract class _BASE {
	public $user;
	public $pass;
	public $key;
	
	const TYPE_SAFE = 1;
	const TYPE_ADULT = 2;
	
	function __construct($details){
		if($details instanceof UserPass) {
			$this->user = $deails->getDetails('username');
			$this->pass = $deails->getDetails('password');
		}
		elseif ($details instanceof ApiKey) {
			$this->key = $deails->getDetails('key');
                }
	}
	
	static function Login(){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_TIMEOUT,30);
		return $ch;
	}
}
?>