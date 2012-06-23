<?php
namespace Utility\DDL\Hosts\Upload;
use Utility\DDL\Hosts\API;

class FileSonic extends Internal\FTPHostBase implements Interfaces\IUploadHost {
	static $__provides = array('lib.ddl.upload.module');
	
	const FTP_HOST = 'ftp.eu.filesonic.com';
	static $host_is = 'fr';
	static $ch;
	
	function prepare(){
		if(!self::$host_is){
			$ch = $this->CH('www.filesonic.com');
			curl_exec($ch);
			$url = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);
			if(preg_match('#\.([a-z]+)$#', $url, $m)){
				self::$host_is = $m[1];
			}
		}
		if(!self::$ch){
			$url = 'http://www.filesonic.'.self::$host_is.'/user/login';
			$ch = $this->CH($url);
			curl_setopt($ch,CURLOPT_POST,true);
			$post = array('email'=>$this->login->getDetails('username'),'password'=>$this->login->getDetails('password'),"rememberMe"=>'1');
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
		curl_setopt ( $ch, CURLOPT_URL, 'http://www.filesonic.'.self::$host_is.'/filesystem/export-all-links' );
		$post = array('typeExport'=>'txt');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		
		$data = curl_exec($ch);
		
		if(preg_match('#http://www.filesonic.([a-z]+)/file/[0-9]*/'.preg_quote($D->file,'#').'[0-9a-zA-Z_/.-]*#',$data,$m)){
			return $m[0];
		}
		
		return new Struct\DelayReturn($page, $D);
	}
	
	// function that upload file
	public function Upload($file) {
		$this->UploadStart($file);
		
		$url = API\FileSonic::UploadURL($this->login->getDetails('username'),$this->login->getDetails('password'));
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