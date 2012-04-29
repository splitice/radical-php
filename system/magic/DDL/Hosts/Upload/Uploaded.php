<?php
namespace DDL\Hosts\Upload;
use DDL\Hosts\Upload\Struct\DelayReturn;

class Uploaded extends Internal\FTPHostBase implements Interfaces\IUploadHost, Interfaces\INoHTTP {
	static $__provides = array('lib.ddl.upload.module');
	
	const FTP_HOST = 'ftp.ul.to';
	
	function ftpFind($page,$D){
		extract($this->prepare());
	
		curl_setopt ( $ch, CURLOPT_URL, 'http://uploaded.to/me/files/list' );
	
		$data = curl_exec($ch);
		if(preg_match('#http://ul.to/[a-zA-Z0-9]*/'.preg_quote($D->file,'#').'[0-9a-zA-Z_/.-]*#',$data,$m)){
			return $m[0];
		}
	
		return new Struct\DelayReturn($page, $D);
	}
	function prepare(){		
		$ch = self::CH ( 'http://uploaded.to/io/login' );
		curl_setopt_array ( $ch, array (CURLOPT_HTTPHEADER => array ('Expect:' ), CURLOPT_HEADER => true));
		
		//Do login
		if($this->login->getUsername() && $this->login->getPassword()){
			$post = array();
			$post["id"] = $this->login->getUsername();
			$post["pw"] = $this->login->getPassword();
			curl_setopt_array ( $ch, array (CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post, CURLOPT_HEADER => true ) );

			$data = curl_exec ( $ch );

			curl_setopt_array ( $ch, array (CURLOPT_POST => false, CURLOPT_POSTFIELDS => false, CURLOPT_HEADER => false ) );
		}
		
		return compact('ch');
	}
	public function Upload($file) {
		return;
	}
}
?>