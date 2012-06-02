<?php
namespace Image\Upload;

class LulzImg extends _BASE implements IUploadHost {
	static $ch = array();
	
	static function Test(){
		$ch = curl_init('http://lulzimg.com/');
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
	function _upload($file){
		$ch = self::Login();
		
		//Setup CURL
		curl_setopt($ch, CURLOPT_URL, 'http://lulzimg.com/app.php');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('image'=>'@'.$file,'uploadtype'=> 'image','submit'=>'submit'));
		
		//Execute
		$data = curl_exec($ch);
		
		//Get URL
		$m = array();
		if(!preg_match_all('#i\.lulzimg\.com\/(?:.+)#', $data, $m)){
			throw new UploadException('Could not match URL data, Upload failed?');
		}
		
		//Add Scheme
		foreach($m[0] as $k=>$v){
			$m[0][$k] = 'http://'.$v;
		}
		
		return $m[0];
	}
	function Upload($file,$file_type,$size='500x500'){
		//Only family safe uploads
		if($file_type == \Image\File::TYPE_ADULT){
			throw new UploadException('LulzImg doesnt allow adult image uploads');
		}
		
		
		$links = $this->_upload($file);
		$url = $thumb = $links[0];
		
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
				
				$filter = new \Image\Filters\Resize($size[0],$size[1]);
				$gd = imagecreatefromstring(file_get_contents($file));
				$gd = $filter->Execute($gd);
				$temp = new \Image\Temp($gd);
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