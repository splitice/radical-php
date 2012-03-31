<?php
namespace DDL\Hosts\Upload;
use DDL\Hosts\API;

class WUpload extends Internal\FTPHostBase implements Interfaces\IUploadHost {
	static $__provides = array('lib.ddl.upload.module');
	
	const FTP_HOST = 'ftp.wupload.com';
	static $ch;
	static $host_is;
	
	function prepare(){
		if(!self::$host_is){
			$ch = $this->CH('www.wupload.com');
			curl_exec($ch);
			$url = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);
			if(preg_match('#\.([a-z]+)($|/)#', $url, $m)){
				self::$host_is = $m[1];
			}
		}
		if(!self::$ch){
			$url = 'http://www.wupload.'.self::$host_is.'/account/login';
			$ch = $this->CH($url);
			curl_setopt($ch,CURLOPT_POST,true);
			$post = array('email'=>$this->login->getUsername(),'password'=>$this->login->getPassword(),'redirect'=>'/',"rememberMe"=>'1');
			curl_setopt($ch,CURLOPT_POSTFIELDS,$post);
			$data = curl_exec($ch);
			self::$ch = $ch;
			return compact('ch');
		}
		$ch = self::$ch;
		return compact('ch');
	}
	
	function ftpFind($page,$D){
		extract($this->prepare());
		curl_setopt ( $ch, CURLOPT_URL, 'http://www.wupload.'.self::$host_is.'/file-manager/export-all-links' );
		$post = array('typeExport'=>'txt');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		
		$data = curl_exec($ch);

		if(preg_match('#http://www.wupload.([a-z]+)/file/[0-9]*/'.preg_quote($D->file,'#').'[0-9a-zA-Z_/.-]*#',$data,$m)){
			return $m[0];
		}
		
		return new Struct\DelayReturn($page, $D);
	}
	
	
	// function that upload file
	public function Upload($file) {
		$this->UploadStart($file);
		
		$url = API\WUpload::UploadURL($this->username,$this->password);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('files[]'=>'@'.$file));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$r = new Struct\MultiReturn ( $ch, array ($this, 'toLink' ) );
		$r->file = basename($file);
		return $r;
	}
	
	function toLink($page,$D) {
		$data = json_decode($page);
		if(!$data){
			return;
		}
		if($data->FSApi_Upload->postFile->status == 'success'){
			return $data->FSApi_Upload->postFile->response->files[0]->url.'/'.$D->file;
		}
	}
}