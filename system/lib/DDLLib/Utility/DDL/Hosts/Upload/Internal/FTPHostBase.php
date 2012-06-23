<?php
namespace Utility\DDL\Hosts\Upload\Internal;
use Utility\DDL\Hosts\Upload\Struct;

abstract class FTPHostBase extends HostBase {
	/**
	 * @var \DDL\Hosts\Upload\Struct\UserPass
	 */
	protected $ftpLogin;
	
	const FTP_HOST = '';
	
	function __construct(Struct\UserPass $login,Struct\UserPass $ftpLogin = null){
		$this->ftpLogin = $ftpLogin;
		parent::__construct($login);
	}
	protected function ftpUrl($host,$file){
		return 'ftp://'.urlencode($this->ftpLogin->getUsername()).':'.urlencode($this->ftpLogin->getDetail('password')).'@'.$host.'/'.basename($file);
	}
	public function FTPUpload($file){
		$host = static::FTP_HOST;
		if(!$host){
			return;//Not supported
		}
		
		$ch = curl_init($this->ftpUrl($host,$file));
		$fp = fopen($file, 'rb');
 		curl_setopt($ch, CURLOPT_UPLOAD, 1);
 		curl_setopt($ch, CURLOPT_INFILE, $fp);

 		return new Struct\MultiReturn ( $ch, array ($this, 'ftpFind' ),basename($file) );
	}
}