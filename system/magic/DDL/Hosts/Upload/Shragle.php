<?php
namespace DDL\Hosts\Upload;
use DDL\Hosts\Upload\Struct\DelayReturn;

class Shragle extends Internal\HostBase implements Interfaces\IUploadHost {
	static $__provides = array('lib.ddl.upload.module');
	
	function prepare(){		
		$ch = self::CH ( 'http://www.shragle.com/login' );
		curl_setopt_array ( $ch, array (CURLOPT_HTTPHEADER => array ('Expect:' ), CURLOPT_HEADER => true, CURLOPT_REFERER=>'http://shragle.com'));
		
		//Do login
		if($this->login->getUsername() && $this->login->getPassword()){
			$post = array();
			$post["submit"] = "Log in";
			$post["username"] = $this->login->getUsername();
			$post["password"] = $this->login->getPassword();
			
			curl_setopt_array ( $ch, array (CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post, CURLOPT_HEADER => true ) );

			$data = curl_exec ( $ch );

			curl_setopt_array ( $ch, array (CURLOPT_POST => false, CURLOPT_POSTFIELDS => false, CURLOPT_HEADER => false ) );
		}
		
		return compact('ch');
	}
	public function Upload($file) {
		$this->UploadStart($file);
		
		extract($this->prepare());
		
		//Main
		$matches = array ();
		curl_setopt ( $ch, CURLOPT_URL, 'http://www.shragle.com/' );
		
		$rnd = time().self::rndNum(3);
    	$rnd_id = self::rndNum(5);

		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		$page = curl_exec($ch);
				
		if(!preg_match('#class="upload"[\r|\n|\s]+action="([^"]+)"#', $page, $up))
		{
			throw new \Exception('Cannot get url action upload.');
		}
		if(!preg_match('#name="MAX_FILE_SIZE"[\r|\n|\s]+value="([^"]+)"#', $page, $max))
		{
			throw new \Exception('Cannot get max file size.');
		}
		if(!preg_match('#name="UPLOAD_IDENTIFIER"[\r|\n|\s]+value="([^"]+)"#', $page, $id))
		{
			throw new \Exception('Cannot get id.');
		}
		if(!preg_match('#name="userID" value="([^"]+)"#', $page, $userid))
		{
			throw new \Exception('Cannot get user id.');
		}
		if(!preg_match('#name="password"[\r|\n|\s]+value="([^"]+)"#', $page, $pas))
		{
			throw new \Exception('Cannot get password.');
		}
		
		curl_setopt ( $ch, CURLOPT_URL, $up[1]);
		
		$che = curl_init($up[1]);
		curl_setopt ( $che, CURLOPT_CONNECTTIMEOUT, 1 );
		curl_setopt ( $che, CURLOPT_TIMEOUT, 1 );
		curl_setopt ( $che, CURLOPT_RETURNTRANSFER, true );
		if(!curl_exec($che)){
			return;
		}
		curl_close($che);
		
		
		//Upload        
		$post = array ();
		$post ['MAX_FILE_SIZE'] = $max[1];
		$post ['userID'] = $userid[1];
		$post ['UPLOAD_IDENTIFIER'] = $id[1];
		$post ['password'] = $pas[1];
		$post ['lang'] = 'en_GB';
		$post ['file_1'] = '@' . $file;

		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt_array ( $ch, array (CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post ) );

		
		$r = new Struct\MultiReturn($ch, array($this,'toLink'));
		$r->file = basename($file);
		return $r;
	}
	
	function toLink($page,$D){
		if(preg_match('#Link: <a href="([^"]+)"#', $page, $m))
		{			
			return $m[1];
		}
	}
}
?>