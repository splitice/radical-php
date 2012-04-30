<?php
namespace DDL\Hosts\Upload\Internal;
use \DDL\Hosts\Upload\Struct\LoginDetails;
use DDL\Hosts\Upload\Exception;

abstract class HostBase extends \Core\Object {
	/**
	 * @var \DDL\Hosts\Upload\Struct\LoginDetails
	 */
	protected $login;
	
	function __construct(LoginDetails $login){
		$this->login = $login;
	}
	
	protected static function rndNum($lg){
		$ret = '';
		for ($i=1;$i<=$lg;$i++){
			$ret .= (string)rand(0,9);
		}
		return $ret;
	}
	
	protected function UploadStart($file){
		if (! file_exists ( $file )) {
			throw new Exception\UploadException ( "The file <b>$file</b> does not exist" );
		}
	}
	protected static function CH($url){
		$ch = curl_init($url);
		curl_setopt_array ( $ch, array ( CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEFILE => 'cookies.txt', CURLOPT_COOKIEJAR => 'cookies.txt', CURLOPT_FOLLOWLOCATION=>true) );
		return $ch;
	}
	
	function noHTTPCallback(){
		
	}
}