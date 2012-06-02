<?php
namespace DDL\Hosts\Upload;
use DDL\Hosts\Upload\Struct\DelayReturn;

class DepositFiles extends Internal\HostBase implements Interfaces\IUploadHost {
	static $__provides = array('lib.ddl.upload.module');
	
	function prepare(){		
		$ch = self::CH ( 'http://www.depositfiles.com/login.php?return=/signup.php' );
		curl_setopt_array ( $ch, array (CURLOPT_HTTPHEADER => array ('Expect:' ), CURLOPT_HEADER => true, CURLOPT_REFERER=>'http://depositfiles.com'));
		
		//Do login
		if($this->login->getUsername() && $this->login->getPassword()){
			$post = array();
			$post["redirect"] = "/";
			$post["login"] = $this->login->getUsername();
			$post["password"] = $this->login->getPassword();
			
			curl_setopt_array ( $ch, array (CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post, CURLOPT_HEADER => true ) );

			$data = curl_exec ( $ch );

			curl_setopt_array ( $ch, array (CURLOPT_POST => false, CURLOPT_POSTFIELDS => false, CURLOPT_HEADER => false ) );
		}
		
		return compact('ch');
	}
	public function Upload($file) {
		$this->UploadStart($file);
		
		extract($this->prepare());
		
		//Main
		$matches = array ();
		curl_setopt ( $ch, CURLOPT_URL, 'http://depositfiles.com/' );
		
		$rnd = time().self::rndNum(3);
    	$rnd_id = self::rndNum(5);

		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		$page = curl_exec($ch);
		
		preg_match('#id="upload_form" method="post" enctype="multipart/form-data" action="([^"]+)"#', $page, $m);
		curl_setopt ( $ch, CURLOPT_URL, $m[1]);
		preg_match('#name="MAX_FILE_SIZE" value="([^"]+)"#', $page, $maxsize);
		preg_match('#name="UPLOAD_IDENTIFIER" value="([^"]+)"#', $page, $uidentifier);
		
		//Upload        
		$post = array ();
		$post ['MAX_FILE_SIZE'] = $maxsize[1];
		$post ['UPLOAD_IDENTIFIER'] = $uidentifier[1];
		$post ['go'] = 1;
		$post ['agree'] = 1;
		$post ['padding'] = '';
		$post ['files'] = '@' . $file;

		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt_array ( $ch, array (CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post ) );

		$r = new Struct\MultiReturn($ch, array($this,'toLink'));
		$r->file = basename($file);
		return $r;
	}
	
	function toLink($page,$D){
		if(preg_match("#parent\\.ud_download_url = '([^']+)'#", $page, $m))
		{			
			return $m[1].'/'.$D->file;
		}		
	}
}
?>