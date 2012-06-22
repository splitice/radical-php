<?php
namespace Utility\Image\Upload;

use Utility\Image;

class Imgur extends _BASE implements IUploadHost {
	static $ch = array();
	
	static function Test(){
		$ch = curl_init('http://imgur.com/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		if(curl_exec($ch)){
			return true;
		}
		return false;
	}
	static function Login($username = null /* used as key */, $password = null){		
		$ch = parent::Login();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'msie');
		return $ch;
	}
	function _upload($file){
		$ch = self::Login($this->key);
		
		//Setup CURL
		curl_setopt($ch, CURLOPT_URL, 'http://api.imgur.com/2/upload.xml');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('image'=>'@'.$file,'key'=> $this->key));
		
		//Execute
		$data = curl_exec($ch);
		
		//Get URL
		$m = array();
		if(!preg_match("~<original>(.*?)</original>~",$data,$m)){
			throw new UploadException('Could not match URL data, Upload failed?');
		}
		
		return $m[1];
	}
	function Upload($file,$file_type,$size='500x500'){
		//Only family safe uploads
		//if($file_type == Image\File::TYPE_ADULT){
		//	throw new UploadException('Imgur doesnt allow adult image uploads');
		//}
		
		
		$link = $this->_upload($file);
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