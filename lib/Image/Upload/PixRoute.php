<?php
namespace Image\Upload;

class PixRoute extends _BASE implements IUploadHost {
	static $ch = array();
	
	static function Test(){
		$ch = curl_init('http://www.pixroute.com/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		if(curl_exec($ch)){
			return true;
		}
		return false;
	}
	static function Login($username = null, $password = null){
		global $_CONFIG;
		
		$key = $username.'_'.$password;
		if(isset(self::$ch[$key])){
			return self::$ch[$key];
		}
		
		$ch = parent::Login();
		curl_setopt($ch, CURLOPT_URL, 'http://www.pixroute.com/login.html');
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		//Create cookie file
		$cookie_file = $_CONFIG ['general']['temp_dir'].'/'.$key.'.cookies';
		if(!file_exists($cookie_file)){
			file_put_contents($cookie_file, '');
		}
		
		//Set cookie storage file
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		
		curl_exec($ch);
		curl_setopt($ch, CURLOPT_URL, 'http://www.pixroute.com/');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$post = array('op'=>'login','redirect'=>'','login'=>$username,'password'=>$password,'x'=>'33','y'=>'13');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		
		if($username && $password){
			curl_exec($ch);
		}
		
		self::$ch[$key] = $ch;
		
		return $ch;
	}
	function Upload($file,$file_type,$size='500x500'){
		
		if($size === null || !is_string($size)){
			$size = '500x500';
		}

		$ch = self::Login($this->user,$this->pass);
		
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_URL, 'http://www.pixroute.com/');
		
		$data = curl_exec($ch);
		
		//Get upload url
		$m = array();
		if(!preg_match('#form name="file" enctype="multipart/form-data" action="(.+)" method="post"#i', $data, $m)){
			throw new UploadException('Could not find Upload Server URL');
		}
		$upload_url = $m[1];
		curl_setopt($ch, CURLOPT_URL, $upload_url);
		
		//Get TMP path
		$m = array();
		if(!preg_match('#name="srv_tmp_url" value="(.+)"#i', $data, $m)){
			throw new UploadException('Could not find Server tmp URL');
		}
		$tmp_path = $m[1];
		
		$m = array();
		if(!preg_match('#name="sess_id" value="(.+)"#i', $data, $m)){
			throw new UploadException('Could not find Session ID');
		}
		$sess_id = $m[1];
		
		//Store and set working directory
		$working_dir = getcwd();
		chdir(dirname($file));
		$post = array('upload_type'=>'file','sess_id'=>$sess_id,'srv_tmp_url'=>$tmp_path,'file_0'=>'@'.basename($file));
		
		if($file_type == self::TYPE_ADULT){
			$post['adult'] = 'adult';
		}else{
			$post['adult'] = 'family';
		}
		
		$post['thumb_size'] = $size;
		
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		
		$data = curl_exec($ch);
		
		//Reset working directory
		chdir($working_dir);
		
		if(!preg_match('#\[URL=([^\]]+)\]\[IMG\]([^\[]+)\[/IMG\]\[/URL\]#', $data, $m)){
			file_put_contents('/tmp/upload.html', $data);
			throw new UploadException('Could not match URL data, Upload failed?');
		}
		
		return array('url'=>$m[1],'thumb'=>$m[2]);
	}
}
?>