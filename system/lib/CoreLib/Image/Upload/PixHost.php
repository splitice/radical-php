<?php
namespace Image\Upload;

class PixHost extends _BASE implements IUploadHost {
	static $ch = array();
	
	static function Test(){
		$ch = curl_init('http://www.pixhost.org/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		if(curl_exec($ch)){
			return true;
		}
		return false;
	}
	static function Login($username = null, $password = null){		
		$ch = parent::Login();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'msie');
		return $ch;
	}
	function Upload($file,$file_type,$size='500x500'){
		$ch = self::Login();
			
		//Get upload url
		$field = 'img[]';
		$m = array();
		if(($size == '500x500' || $size === null) && (((float)substr(PHP_VERSION,0,3)) >= 5.4)){
			$field = '0';
			$url = 'http://www.pixhost.org/cover-upload/';
		}else{
			$url = 'http://www.pixhost.org/classic-upload/';
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_exec($ch);
		
		curl_setopt($ch, CURLOPT_REFERER, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		
		$working_dir = getcwd();
		chdir(dirname($file));
		$post = array($field=>'@'.basename($file));
		
		if($field == '0'){//Cover
			$post['1'] = '';
			$post['2'] = '';
			$post['3'] = '';
		}
		
		if($file_type == self::TYPE_ADULT){
			$post['content_type'] = '1';
		}else{
			$post['content_type'] = '0';
		}
		
		$post['tos'] = 'on';
		
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		
		$data = curl_exec($ch);
		//Reset working directory
		chdir($working_dir);
		
		if(!preg_match('#\[url=([^\]]+)\]\[img\]([^\[]+)\[/img\]\[/url\]#', $data, $m)){
			throw new UploadException('Could not match URL data, Upload failed?');
		}

		return array('url'=>$m[1],'thumb'=>$m[2]);
	}
}
?>