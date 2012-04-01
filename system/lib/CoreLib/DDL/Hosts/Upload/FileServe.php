<?php
namespace DDL\Hosts\Upload;
class FileServe extends Internal\FTPHostBase implements Interfaces\IUploadHost {
	static $__provides = array('lib.ddl.upload.module');
	const FTP_HOST = 'ftp.fileserve.com';
	
	function ftpFind($page,$D){
		extract($this->prepare());
		
		curl_setopt ( $ch, CURLOPT_URL, 'http://www.fileserve.com/file-manager.php' );
		$post = array('fileNameSearch'=>$D->file,'fileSearchFormSubmit'=>'Search');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		
		$data = curl_exec($ch);
		if(preg_match('#none\'\>(http://www.fileserve.com/file/[a-zA-Z0-9]*/'.preg_quote($D->file,'#').'[0-9a-zA-Z_/.-]*)#',$data,$m)){
			return $m[1];
		}
		
		return new Struct\DelayReturn($page, $D);
	}
	function prepare(){		
		$ch = self::CH ( 'http://www.fileserve.com/login.php' );
		curl_setopt_array ( $ch, array (CURLOPT_HTTPHEADER => array ('Expect:' ), CURLOPT_HEADER => true, CURLOPT_REFERER=>'http://fileserve.com/index.php'));
		
		//Do login
		if($this->login->getUsername() && $this->login->getPassword()){
			$post = array ("autoLogin"=>1,"loginUserName"=>$this->login->getUsername(),"loginUserPassword"=>$this->login->getPassword(),"loginFormSubmit"=>1);
			curl_setopt_array ( $ch, array (CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post ) );
			curl_exec ( $ch );
			curl_setopt_array ( $ch, array (CURLOPT_POST => false, CURLOPT_POSTFIELDS => false ) );
		}
		
		return compact('ch');
	}
	public function Upload($file) {
		$this->UploadStart($file);
		
		extract($this->prepare());
		
		//Main
		$matches = array ();
		curl_setopt ( $ch, CURLOPT_URL, 'http://www.fileserve.com/' );
		$page = curl_exec ( $ch );
		if(!preg_match('#action="http://upload\.fileserve\.com/upload/(.*)"#', $page, $match)){
			return false;
		}
		
		$rnd = time().self::rndNum(3);
    	$rnd_id = self::rndNum(5);

    	$id = $match[1];
    	
		$uploadHostURL = 'http://upload.fileserve.com/upload/'.$id.'?callback=jsonp'.$rnd."&_=".$rnd_id;
		$cht = curl_init($uploadHostURL);
		curl_setopt ( $cht, CURLOPT_RETURNTRANSFER, true );
		$page = curl_exec ( $cht );
		
		preg_match('#sessionId\:\'(.*)\'}#', $page, $match);
		$uploadSessionId = $match[1];
		
		$up_loc = 'http://upload.fileserve.com/upload/'.$id . $uploadSessionId ;

		//Upload               
		$post = array ();
		$post ['file'] = '@' . $file;

		curl_setopt ( $ch, CURLOPT_URL, $up_loc );
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt_array ( $ch, array (CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post ) );

		$r = new Struct\MultiReturn($ch, array($this,'toLink'));
		$r->file = basename($file);
		return $r;
	}
	
	function toLink($page,$D){
		if(preg_match('#"shortenCode":"(.*)"}#', $page, $match)){
			return "http://www.fileserve.com/file/" . $match[1].'/'.$D->file;
		}
	}
}
?>