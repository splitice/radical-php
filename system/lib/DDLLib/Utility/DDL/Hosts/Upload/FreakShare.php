<?php
namespace DDL\Hosts\Upload;
use DDL\Hosts\Upload\Struct\DelayReturn;

class FreakShare extends Internal\HostBase implements Interfaces\IUploadHost {
	static $__provides = array('lib.ddl.upload.module');
	
	function prepare(){		
		$ch = self::CH ( 'http://freakshare.com/login.html' );
		curl_setopt_array ( $ch, array (CURLOPT_HTTPHEADER => array ('Expect:' ), CURLOPT_HEADER => true, CURLOPT_REFERER=>'http://freakshare.com/login.html'));
		
		//Do login
		if($this->login->getUsername() && $this->login->getPassword()){
			$post = array();
			$post["user"] = $this->login->getUsername();
			$post["pass"] = $this->login->getPassword();
			$post["submit"] = 'Login';
			curl_setopt_array ( $ch, array (CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post, CURLOPT_HEADER => true ) );

			$data = curl_exec ( $ch );
			curl_setopt_array ( $ch, array (CURLOPT_POST => false, CURLOPT_POSTFIELDS => false, CURLOPT_HEADER => false ) );
		}
		
		return compact('ch','cookie');
	}
	public function Upload($file) {
		$this->UploadStart($file);
		
		extract($this->prepare());
		
		//Main
		$matches = array ();
		curl_setopt($ch,CURLOPT_URL,'http://freakshare.com/');
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		$page = curl_exec($ch);
		
		preg_match('#<form action="([^"]+)"#',$page,$url);
		
		$url = $url[1].'?X-Progress-ID='.md5(time().'|'.microtime(true).'|'.rand(0,1000));
		curl_setopt($ch,CURLOPT_URL,$url);
		
		//Upload               
		preg_match('#name="APC_UPLOAD_PROGRESS" id="progress_key"  value="([^"]+)"#',$page,$m1);
		preg_match('#name="APC_UPLOAD_USERGROUP" id="usergroup_key"  value="([^"]+)"#',$page,$m2);
		preg_match('#name="UPLOAD_IDENTIFIER" value="([^"]+)"#',$page,$m3);
		
		$cwd = getcwd();
		
		$post = array(
                  'APC_UPLOAD_PROGRESS' => $m1[1],
                  'APC_UPLOAD_USERGROUP' => $m2[1],
                  'UPLOAD_IDENTIFIER' => $m3[1],
                  'file[]' => basename($file),
                  'file[1]' => "@".basename($file) 
               	);
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt_array ( $ch, array (CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post ) );
		
		$r = new Struct\MultiReturn($ch, array($this,'toLink'));
		$r->file = basename($file);
		return $r;
	}
	
	function toLink($page,$D){
		if(preg_match('#<input type="text" value="(http://freakshare.com/files/[^"]+)"#',$page,$m)){
			return $m[1];
		}
	}
}
?>