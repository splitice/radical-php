<?php
namespace DDL\Hosts\Upload;
use DDL\Hosts\Upload\Struct\DelayReturn;

class FileFactory extends Internal\HostBase implements Interfaces\IUploadHost {
	static $__provides = array('lib.ddl.upload.module');
	private $cookieff;
	
	function prepare(){		
		$ch = self::CH ( 'http://www.filefactory.com/member/login.php' );
		curl_setopt_array ( $ch, array (CURLOPT_HTTPHEADER => array ('Expect:' ), CURLOPT_HEADER => true, CURLOPT_REFERER=>'http://filefactory.com'));
		
		if($this->cookieff){
			$cookie = $this->cookieff;
		}
		
		//Do login
		if($this->login->getUsername() && $this->login->getPassword()){
			$post = array();
			$post["redirect"] = "/";
			$post["email"] = $this->login->getUsername();
			$post["password"] = $this->login->getPassword();
			
			curl_setopt_array ( $ch, array (CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post, CURLOPT_HEADER => true ) );

			$data = curl_exec ( $ch );

			if(preg_match_all('#ff_membership=([^\;]+)\;#', $data, $m)){
				$this->cookieff = $cookie = array_pop($m[1]);
			}else{
				$f = file_get_contents('cookies.txt');
				if(preg_match('#ff_membership\t(.+)#',$f,$m)){
					$cookie = $m[1];
				}else{
					throw new \Exception('Error');
				}
			}
			curl_setopt_array ( $ch, array (CURLOPT_POST => false, CURLOPT_POSTFIELDS => false, CURLOPT_HEADER => false ) );
		}
		
		return compact('ch','cookie');
	}
	public function Upload($file) {
		$this->UploadStart($file);
		
		extract($this->prepare());
		
		//Main
		$matches = array ();
		$ch = curl_init();
		curl_setopt ( $ch, CURLOPT_URL, 'http://upload.filefactory.com/upload.php' );
		
		$rnd = time().self::rndNum(3);
    	$rnd_id = self::rndNum(5);

		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		
		//Upload               
		$post = array ();
		$post ['Filename'] = basename($file);
		$post ['FileData'] = '@' . $file;
		$post ['cookie'] = urldecode($cookie);
		$post ['folderViewhash'] = 0;
		$post ['Upload'] = 'Submit Query';	

		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt_array ( $ch, array (CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post ) );

		$r = new Struct\MultiReturn($ch, array($this,'toLink'));
		$r->file = basename($file);
		return $r;
	}
	
	function toLink($page,$D){
		if($page){
			if(strpos($page,'Upload Failed:') !== false){
				return;
			}
			return "http://www.filefactory.com/file/" . $page .'/'.$D->file;
		}else{
			$url = 'http://www.filefactory.com/manager/get.php?_='.time().'&m=getFiles&f=0&p=1&s='.urlencode($D->file).'&so=DESC&ss=sortCreated';
			extract($this->prepare());
			curl_setopt ( $ch, CURLOPT_URL, $url );
			
			$data = curl_exec($ch);
			//$data = preg_replace('/(\w+):/i', '"\1":', $data);
				
			//$data = preg_replace('/([{,])(\s*)([^"]+?)\s*:/','$1"$3":',$data);
			$J = new \Basic\JSON\Support();
			$json = $J->decode($data);
			
			if(!$json || !isset($json->files)) {
				return;
			}
			
			foreach($json->files as $file){
				return $file->url;
			}
			
			\CLI\Output\Error::Notice('Waiting 10 seconds');
			return new DelayReturn($page, $D, 10);
		}
	}
}
?>