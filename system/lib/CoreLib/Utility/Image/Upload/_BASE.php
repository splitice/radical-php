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
			$details_array = $deails->getDetails;
			$this->user = $details_array[0]
			$this->pass = $details_array[1]
		}
		elseif ($details instanceof ApiKey)
			$details_array = $deails->getDetails;
			$this->key = $details_array[0]
	}
	
	static function Login(){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_TIMEOUT,30);
		return $ch;
	}
}
?>