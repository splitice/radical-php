<?php
namespace Utility\Image\Upload;

use Utility\Image;

class Imgur extends _BASE implements IUploadHost {
	static $ch = array();
	static $cookies;
	
	static function Test(){
		$ch = curl_init('http://imagetwist.com/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		if(curl_exec($ch)){
			return true;
		}
		return false;
	}
	static function Login($username = null, $password = null){		
		$ch = parent::Login();
		//Setup CURL
		curl_setopt($ch, CURLOPT_URL, 'http://imagetwist.com/');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('op'=>'login','login'=>$username,'password'=>$password,'submit'=>'Login'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'msie');
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookies);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookies);
		
		//Execute
		$data = curl_exec($ch);
		return $ch;
	}
	function _upload($file, $file_type){
		$ch = self::Login($this->user, $this->pass);
		
		//Generate UID
		$uid = '';
		for($i=0;$i<12;$i++) $uid .= rand(0,9);
		
		//Get Upload url and session id to generate URL
		$upurl = cut_str($page,'<form name="url" enctype="multipart/form-data" action="','" method="post"');
		$sid = cut_str($page,'name="sess_id" value="','">');
		$url = $upurl.$uid.'&js_on=1&utype=reg&upload_type=url';
		
		//Setup CURL
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('file_0'=>'@'.$file,'sess_id'=>$sid,'upload_type'=>'url','mass_upload'=>1,'thumb_size'=>'170x170','file_safe'=>($file_type-1),'fld_id'=>0,'per_row'=>'1','tos'=>1,'submit_btn'=>'','file_1'=>''));
		
		//Execute
		$data = curl_exec($ch);
		
		//Get Fn
		$m = array();
		if(!preg_match("~<textarea name='fn'>([a-zA-Z0-9]+)</textarea>~ism",$data,$m)){
			throw new UploadException('Could not match URL data, Upload failed?');
		}
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('fn'=>$m[1],'st'=>'OK','op'=>'upload_result','per_row'=>'1'));
		
		//Execute
		$data = curl_exec($ch);
		
		//Get URL
		$m = array();
		if(!preg_match("~<tr><td align=center><b>Code for forums:</b></td>\s+<td><input(.*?)value=\"(.*?)\"></td><td><input(.*?)></td></td></tr>~ism",$data,$m)){
			throw new UploadException('Could not match URL data, Upload failed?');
		}
		
		return $m[2];
	}
	function Upload($file,$file_type,$size='500x500'){
		//Only family safe uploads
		//if($file_type == Image\File::TYPE_ADULT){
		//	throw new UploadException('ImageTwist doesnt allow adult image uploads');
		//}
		
		
		$link = $this->_upload($file, $file_type);
		$url = $thumb = $links;
		
		if($size !== null){
			//Parse size
			$size = explode('x',$size);
			if(count($size) != 2){
				throw new UploadException('Invalid upload size');
			}
			
			//Upload Thumbnail
			$imgSize = getimagesize($file);
			if($size[0] < $imgSize[0] || $size[1] < $imgSize[1]){
				//Scaling
				if($imgSize[0]>$imgSize[1]){
					$size[1] = 'auto';
				}else{
					$size[0] = 'auto';
				}
				
				$filter = new Image\Filters\Resize($size[0],$size[1]);
				$gd = imagecreatefromstring(file_get_contents($file));
				$gd = $filter->Execute($gd);
				$temp = new Image\Temp($gd);
				$thumb = $temp->Upload($this, $file_type, null);
				if($thumb){
					$thumb = $thumb['url'];
				}
			}
		}

		return array('url'=>$url,'thumb'=>$thumb);
	}
}
?>