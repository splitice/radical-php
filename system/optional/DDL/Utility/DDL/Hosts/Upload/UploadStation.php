<?php
namespace Utility\DDL\Hosts\Upload;

class UploadStation extends Internal\FTPHostBase implements Interfaces\IUploadHost {
	static $__provides = array('lib.ddl.upload.module');
	
	const FTP_HOST = 'ftp.uploadstation.com';
	
	function ftpUrl($host,$file){
		global $_CONFIG;
		return 'ftp://'.urlencode($this->username).':'.$_CONFIG['upload']['hosts']['UploadStation']['ftp_password'].'@'.$host.'/'.basename($file);
	}
	function ftpFind($page,$D){
		extract($this->prepare());
		
		curl_setopt ( $ch, CURLOPT_URL, 'http://www.uploadstation.com/myfiles.php' );
		$post = array('fileNameSearch'=>$D->file,'fileSearchFormSubmit'=>'Search');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		
		$data = curl_exec($ch);
		if(preg_match('#http://www.uploadstation.com/file/[a-zA-Z0-9]*/'.preg_quote($D->file,'#').'[0-9a-zA-Z_/.-]*#',$data,$m)){
			return $m[0];
		}
		
		return new Struct\DelayReturn($page, $D);
	}
	function prepare(){
		//Login
		$post = array ("autoLogin"=>'on',"loginUserName"=>$this->login->getUsername(),"loginUserPassword"=>$this->login->getPassword(),"loginFormSubmit"=>'Login');
		$ch = self::CH ( 'http://www.uploadstation.com/login.php' );
		//curl_setopt ( $ch, CURLOPT_HTTPHEADER, array ('Expect:' ) );
		curl_setopt_array ( $ch, array (CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEFILE => 'cookies.txt', CURLOPT_COOKIEJAR => 'cookies.txt', CURLOPT_FOLLOWLOCATION => true, CURLOPT_HEADER => true, CURLOPT_USERAGENT=>"Moz"));
		
		//Do login
		if($this->login->hasDetails()){
			curl_setopt_array ( $ch, array (CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post ) );
			$page = curl_exec ( $ch );
		}
		
		return compact('ch','page');
	}
	public function upload($file) {
		$this->UploadStart($file);
		
		extract($this->prepare());
		
		//Main
		$matches = array ();
		curl_setopt ( $ch, CURLOPT_URL, 'http://www.uploadstation.com/upload.php' );
		curl_setopt_array ( $ch, array (CURLOPT_POST => false, CURLOPT_POSTFIELDS => false ) );
		$page = curl_exec ( $ch );
		if(strpos($page,'<h1>500 GB Storage Quota Exceeded</h1>')){
			\CLI\Output\Error::Warning('UploadStation: 500 GB Storage Quota Exceeded');
			return;
		}
		if(!preg_match('@id="uploadForm"[\r|\n|\s|.]+action="([^"]+)"@i', $page, $act)){
			//echo('form action');
			return;
		}
		$rnd = time().self::rndNum(3);
    	$rnd_id = self::rndNum(5);

		$uploadHostURL = $act[1].'?callback=jsonp'.$rnd."&_=".$rnd_id;
		//curl_setopt ( , CURLOPT_URL, $uploadHostURL );
		$cht = curl_init($uploadHostURL);
		curl_setopt($cht,CURLOPT_RETURNTRANSFER, true);
		$page = curl_exec ( $cht );
		if(!preg_match("@sessionId:'([^']+)'@i", $page, $sid)){
			//echo('Cannot get Session ID.'); // "Houston, We've Got a Problem"
			return;
		}
		
		$up_loc = $act[1] . $sid[1];
		
		//Upload               
		$post = array ();
		$post ['file'] = '@' . $file;
		
		curl_setopt ( $ch, CURLOPT_URL, $up_loc );
		curl_setopt_array ( $ch, array (CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post ) );
		
		return new Struct\MultiReturn($ch, array($this,'toLink'));
	}
	
	function toLink($upfiles,$D){		
		if(preg_match('@"shortenCode":"([^"]+)"@i', $upfiles, $sc)) {
			$download_link = "http://www.uploadstation.com/file/" . $sc[1];
			if(preg_match('@"deleteCode":"([^"]+)"@i', $upfiles, $del)) {
				$delete_link = $download_link . "/delete/" . $del[1];
			} else {
				$delete_link = "ERROR: Deletion-Link not Found.";
			}
			if(preg_match('@"fileName":"([^"]+)"@i', $upfiles, $fn)) {
				$download_link .= "/" . $fn[1];
			}
			return $download_link;
		}
	}
}
?>