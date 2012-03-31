<?php
namespace DDL\Hosts\Upload;

class FilePost extends Internal\FTPHostBase implements Interfaces\IUploadHost {
	static $__provides = array('lib.ddl.upload.module');
	const FTP_HOST = 'ftp-eu.filepost.com';

	function ftpFind($page,$D){
		extract($this->prepare());
		curl_setopt ( $ch, CURLOPT_URL, 'http://filepost.com/files/manager/?SID='.$sid.'&JsHttpRequest='.time().'-xml' );
		/*
		
		*/
		$post = 'folder_id=0&status=active&date_from=unundefinedefined-undefined-d&date_to=unundefinedefined-undefined-d&
		file_group=0&file_type=0&upload_type=0&sorting_field=create_date&sorting_type=desc&page=1&per_page=50&
		date_from_view=08/11/2011&date_to_view=08/11/2011&search_string='.urlencode($D->file).'&action=search_files&token=fl4eb8ed78a470a';//array('folder_id'=>0,'status'=>'active','search_string'=>$D->file);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		
		$data = curl_exec($ch);
		
		$ret = json_decode($data);
		if(isset($ret->js) && isset($ret->js->files_box)){
			$data = $ret->js->files_box;
			//http://filepost.com/files/dbf6a2cb/1.jpg/
			if(preg_match('#filepost.com/files/[a-zA-Z0-9]*/'.preg_quote($D->file,'#').'[0-9a-zA-Z_/.-]*#',$data,$m)){
				return 'http://www.'.$m[0];
			}
		}
		
		return new Struct\DelayReturn($page, $D);
	}
	function prepare(){
		//Login
		$post = array ("remember"=>'on',"email"=>$this->login->getUsername(),"password"=>$this->login->getPassword());
		$ch = self::CH ( 'http://filepost.com/general/login_form/' );
		//curl_setopt ( $ch, CURLOPT_HTTPHEADER, array ('Expect:' ) );
		curl_setopt_array ( $ch, array (CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER=> true, CURLOPT_COOKIEFILE => 'cookies.txt', CURLOPT_COOKIEJAR => 'cookies.txt', CURLOPT_FOLLOWLOCATION => true, CURLOPT_HEADER => true, CURLOPT_USERAGENT=>"Moz"));
		
		//Do login
		if($this->login->getUsername() && $this->login->getPassword()){
			curl_setopt_array ( $ch, array (CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post ) );
			$page = curl_exec ( $ch );
		}
		
		if(preg_match('#SID: \'([^\']+)#',$page,$m)){
			$sid = $m[1];
		}
		
		curl_setopt($ch,CURLOPT_HEADER,false);
		
		return compact('ch','page','sid');
	}
	public function Upload($file) {
		$this->UploadStart($file);
		
		extract($this->prepare());
		
		//Main
		$matches = array ();
		curl_setopt ( $ch, CURLOPT_URL, 'http://filepost.com/files/upload/' );
		curl_setopt_array ( $ch, array (CURLOPT_POST => false, CURLOPT_POSTFIELDS => false ) );
		$result = curl_exec ( $ch );
		
		preg_match("#upload_url: '(.*)'#",$result,$upURL);
		$upURL =  $upURL[1];
		preg_match("#SID: '(.*)'#",$result,$SID);
		$SID = $SID[1];

		$post = array();
        $post['file']="@$file";
        $post['SID']=$SID;
        $post['Filename']= basename($file);
        $post['Upload']="Submit Query";
        
		curl_setopt($ch, CURLOPT_URL, $upURL);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Shockwave Flash');
		curl_setopt($ch, CURLOPT_POST,true);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_HEADER, 1);

		return new Struct\MultiReturn($ch, array($this,'toLink'));
	}
	
	function toLink($result,$D){		
		preg_match("#answer\":\"(.*)\"#",$result,$answer);
        $answer = $answer[1];
        $ch = $D->ch;
        if ($answer){
        	$url = 'http://filepost.com/files/done/'.$answer.'/?JsHttpRequest';
        	curl_setopt($ch, CURLOPT_POST,false);
        	curl_setopt($ch, CURLOPT_URL, $url);
        	$result = curl_exec($ch);

        	preg_match("#id=\"down_link\" class=\"inp_text\" value=\"(.*)\"#",$result,$link);
        	if($link) {
        		return rtrim($link[1],'/');
        	}
		}
	}
}
?>