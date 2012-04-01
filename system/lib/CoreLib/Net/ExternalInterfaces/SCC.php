<?php
namespace Net\ExternalInterfaces;

class SCC {
	static $ch;
	
	private static function CookieFile($ch){
		$file = '/tmp/SCC.cookie';
		if(!file_exists($file)){
			file_put_contents($file, '');
			chmod($file, 777);
		}
		
		curl_setopt($ch,CURLOPT_COOKIEJAR,$file);
		curl_setopt($ch,CURLOPT_COOKIEFILE,$file);
	}
	static function Login($username,$password){
		if(self::$ch){
			return new self(self::$ch);
		}
		
		$url = 'http://www.sceneaccess.org/login';
		
		$ch = curl_init($url);
		curl_setopt($ch,CURLOPT_POST,true);
		//curl_setopt($ch,CURLOPT_REFERER,'NA');
		self::CookieFile($ch);
		
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
		curl_setopt($ch,CURLOPT_REFERER,$url);
		$post = array('username'=>$username,'password'=>$password,'submit'=>'come on in');
		curl_setopt($ch,CURLOPT_POSTFIELDS,$post);
		
		$data = curl_exec($ch);

		self::$ch = $ch;
		return new self($ch);
	}
	
	function __construct($ch){
		$this->curl = $ch;
	}
	
	function SendInvite($channels = array('announce')){
		$ch = $this->curl;
		
		curl_setopt($ch,CURLOPT_URL,'http://www.sceneaccess.org/irc');
		$post = array();
		
		foreach($channels as $c){
			$post[$c] = 'yes';
		}
		
		curl_setopt($ch,CURLOPT_POSTFIELDS,$post);
		
		$data = curl_exec($ch);
		
		echo "Invited\r\n";
	}
}