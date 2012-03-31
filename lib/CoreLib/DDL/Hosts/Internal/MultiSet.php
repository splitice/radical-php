<?php
namespace DDL\Hosts\Internal;

use DDL\Hosts\Upload\Interfaces\INoHTTP;

use CLI\Console\Progress\Container;
use DDL\Hosts\HandleMulti;
use DDL\Hosts\Upload\Struct\DelayReturn;

class MultiSet {
	protected $set = array();
	protected $multi;
	
	function __construct(array $set,HandleMulti $mh){
		$this->multi = $mh;
		foreach($set as $s){
			$this->addUpload($s);
		}
	}
	
	function addUpload(MultiUpload $s){
		$s->getModule()->setMulti($this);
		$this->set[] = $s;
	}
	
	function removeUpload($s){
		if(is_object($s) && $s instanceof MultiUpload){
			foreach($this->set as $k=>$v){
				if($s == $v){
					unset($this->set[$k]);
				}
			}
		}else{
			unset($this->set[$s]);
		}
	}
	
	function Execute(){
		$links = array();
		
		//Run loop
		$attempt = 0;
		do{
			++$attempt;
				
			\CLI\Output\Error::Notice("Upload attempt ".$attempt.", Uploading ".count($this->set)." instances");
			
			$temp = $this->ExecuteRun ();
			
			//Put returned links into $links array
			foreach($temp as $host=>$v){
				if(!isset($links[$host])){
					$links[$host] = $v;
				}else{
					$links[$host] = array_merge($links[$host],$v);
				}
			}
		}while($this->set);

		return $links;
	}
	private function prepare($mh){
		$handleObjects = array ();
		
		foreach ( $this->set as $upload ) {
			/*if($r['try_count']>=$_CONFIG['upload']['MAX_HTTP_RETRY'] || $this->ftp){
			 \CLI\Output\Error::Notice("Uploading ".$r['file']." to ".$r['host']." via FTP");
			$return = $r['module']->FTPUpload ( $r['file'] );
			}else{
			\CLI\Output\Error::Notice("Uploading ".$r['file']." to ".$r['host']." via HTTP");
		
			}*/
			$return = $upload->Upload();
				
			if($return){
				if($this->multi->getProgress()){
					$progress = $this->multi->getProgress();
					$progress = new $progress();
					curl_setopt ( $return->CH (), CURLOPT_NOPROGRESS, false );
					curl_setopt($return->CH(), CURLOPT_PROGRESSFUNCTION, array($progress,'ProgressFunction'));
					$upload->setOnFailure(array($progress,'Done'));
				}
				curl_setopt($return->CH (), CURLOPT_TIMEOUT, (20*60));//10 mins max
				curl_multi_add_handle ( $mh, $return->CH () );
		
				$handleObjects [] = array('return'=>$return,'upload'=>$upload);
			}else{
				if(!($upload->getModule() instanceof INoHTTP)){
					\CLI\Output\Error::Notice("Upload ".$upload->getFile()." to ".$upload->getHost()." failed due to no curl handle object returned");
				}
				$upload->onFailure();
			}
		}
		
		return $handleObjects;
	}
	function ExecuteRun() {
		global $_CONFIG;
		$links = array ();
	
		//Add handles, upload simultaniously
		$mh = curl_multi_init ();
		$handleObjects = $this->prepare($mh);
	
		//Do execution
		$running = $mrc = null;
		do {
			$mrc = curl_multi_exec ( $mh, $running );
		} while ( $mrc == CURLM_CALL_MULTI_PERFORM );
	
		while ( $running && $mrc == CURLM_OK ) {
			//echo '.';
			if (curl_multi_select ( $mh ) != - 1) {
				do {
					$mrc = curl_multi_exec ( $mh, $running );
				} while ( $mrc == CURLM_CALL_MULTI_PERFORM );
			}
		}
		//echo ';';
	
		//Do callbacks
		foreach ( $handleObjects as $r ) {
			//Get Curl handle
			$ch = $r['return']->CH ();
			
			//Get returned data
			$c = curl_multi_getcontent ( $ch );
			
			//get scheme (FTP or HTTP)
			$scheme = parse_url(curl_getinfo($ch,CURLINFO_EFFECTIVE_URL),PHP_URL_SCHEME);
				
			//Process Callback to get link
			$link = $r ['return']->Callback ( $c );
			
			if($link){
				$host = $r['upload']->getHost();
					
				//Add link to $links[$host]
				if (! isset ( $links [$host] )) {
					$links [$host] = array ();
				}
				$links [$host] [$r ['upload']->getFileNumber()] = $link;
					
				//Now that we have completed this, remove it.
				$this->removeUpload($r['upload']);
			}else{
				if ($c || ($scheme == 'ftp')) {
					\CLI\Output\Error::Notice("Upload ".$r['upload']->getFile()." to ".$r['upload']->getHost()." failed due to no returned link");
					$r['upload']->onFailure();
				}else{
					\CLI\Output\Error::Notice("Upload ".$r['upload']->getFile()." to ".$r['upload']->getHost()." failed due to no http data returned");
					$r['upload']->onFailure();
				}
			}
				
			//Cleanup
			curl_multi_remove_handle ( $mh, $ch );
			curl_close ( $ch );
		}
	
		//Cleanup
		curl_multi_close($mh);
	
		//Return the fruits of our labour
		return $links;
	}
	function Clear(){
		foreach($this->set as $s){
			$s->Clear();
		}
		$this->set = array();
	}
}