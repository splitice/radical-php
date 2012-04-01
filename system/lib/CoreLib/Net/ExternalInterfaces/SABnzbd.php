<?php
namespace Net\ExternalInterfaces;
class SABnzbd {
	private $data;
	
	function __construct($data){
		$this->data = $data;
	}
	
	function getName(){
		return $this->data["name"];
	}
	
	function getPath() {
		return $this->data['storage'];
	}
	function getError(){
		return $this->data["fail_message"];
	}
	function getCategory(){
		return $this->data['category'];
	}
	
	static function getHost(){
		global $_CONFIG;
		return 'http://'.$_CONFIG['usenet']['host'].':'.$_CONFIG['usenet']['port'];
	}
	static function getUrl($mode){
		global $_CONFIG;
		$url = static::getHost();
		$url .= '/sabnzbd/api?mode='.urlencode($mode).'&output=json&apikey='.$_CONFIG['usenet']['API_KEY'];
		return $url;
	}
	static function get($url){
		//Fetch
		$ch = curl_init($url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$data = curl_exec($ch);
		curl_close($ch);
		
		//Error
		if($data === false){
			throw new SABConnectionException("Could not connect to ".static::getHost());
		}
		
		//Decode
		$data = json_decode($data,true);
		
		return $data;
	}
	static function ListHistory($start=0,$limit=50,$complete=true){
		$url = static::getUrl('history');
		$url .= '&start='.(int)$start.'&limit='.(int)$limit;

		$data = static::get($url);

		$ret = array();
		foreach($data['history']['slots'] as $d){
			if(!$complete || $d['status'] == 'Completed'){
				if($d['category'] != '*'){
					$ret[] =new SABnzbd($d);
				}
			}
		}
		
		return $ret;
	}
	static function AddNZB(\File $file,$category,$pp=3,$priority=-1){
		$url = static::getUrl('addlocalfile');
		$url .='&name='.urlencode((string)$file).'&pp='.$pp.'&priority'.$priority.'&cat='.urlencode($category);
		
		$data = static::get($url);
		
		return $data['status'];
	}
}

class SABConnectionException extends \Exception {
}