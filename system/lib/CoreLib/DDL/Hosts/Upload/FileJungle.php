<?php
namespace DDL\Hosts\Upload;

class FileJungle extends Internal\FTPHostBase implements Interfaces\IUploadHost {
	static $__provides = array('lib.ddl.upload.module');
	const FTP_HOST = 'ftp.filejungle.com';
	
	function ftpFind($page,$D){
		extract($this->prepare());
		
		curl_setopt ( $ch, CURLOPT_URL, 'http://filejungle.com/filesystem.php' );
		$post = array('fileNameSearch'=>$D->file,'fileSearchFormSubmit'=>'Search');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		
		$data = curl_exec($ch);
		if(preg_match('#http://www.filejungle.com/f/[a-zA-Z0-9]*/'.preg_quote($D->file,'#').'[0-9a-zA-Z_/.-]*#',$data,$m)){
			return $m[0];
		}
		
		return new Struct\DelayReturn($page, $D);
	}
	function prepare(){
		//Login
		$post = array ("autoLogin"=>'on',"loginUserName"=>$this->login->getUsername(),"loginUserPassword"=>$this->login->getPassword(),"loginFormSubmit"=>'Login');
		$ch = self::CH ( 'http://filejungle.com/login.php' );
		//curl_setopt ( $ch, CURLOPT_HTTPHEADER, array ('Expect:' ) );
		curl_setopt_array ( $ch, array (CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEFILE => 'cookies.txt', CURLOPT_COOKIEJAR => 'cookies.txt', CURLOPT_FOLLOWLOCATION => true, CURLOPT_HEADER => true, CURLOPT_USERAGENT=>"Moz"));
		
		//Do login
		if($this->login->getUsername() && $this->login->getPassword()){
			curl_setopt_array ( $ch, array (CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post ) );
			curl_exec ( $ch );
		}
		
		return compact('ch');
	}
	public function Upload($file) {	
		$this->UploadStart($file);
		
		extract($this->prepare());
		
		//Main
		$matches = array ();
		curl_setopt ( $ch, CURLOPT_URL, 'http://filejungle.com/upload.php' );
		curl_setopt_array ( $ch, array (CURLOPT_POST => false, CURLOPT_POSTFIELDS => false ) );
		$page = curl_exec ( $ch );
		if(!preg_match('@uploadUrl = \'([^\']+)\'@i', $page, $act)){
			throw new Exception\UploadException('couldnt match upload url');
		}

		$cht = curl_init($act[1]. "/new?callback=jsonp" . round(microtime(true) * 1000));
		curl_setopt($cht,CURLOPT_RETURNTRANSFER, true);
		$page = curl_exec ( $cht );
		if(!preg_match("@sessionId:'([^']+)'@i", $page, $sid)){
			throw new Exception\UploadException('Cannot get Session ID.'); // "Houston, We've Got a Problem"
		}
		
		$up_loc = $act[1] .'/s/'. $sid[1].'/up';
		
		curl_setopt ( $ch, CURLOPT_URL, $up_loc );
		curl_setopt_array ( $ch, array (CURLOPT_POST => true, CURLOPT_POSTFIELDS => array ('file'=>'@' . $file) ) );
		
		return new Struct\MultiReturn($ch, array($this,'toLink'));
	}
	
	function toLink($upfiles,$D){		
		if(preg_match('@"shortenCode":"([^"]+)"@i', $upfiles, $sc)) {
			$download_link = "http://www.filejungle.com/f/" . $sc[1];
			if(preg_match('@"fileName":"([^"]+)"@i', $upfiles, $fn)) {
				$download_link .= "/" . $fn[1];
			}
			return $download_link;
		}
	}
}
?>