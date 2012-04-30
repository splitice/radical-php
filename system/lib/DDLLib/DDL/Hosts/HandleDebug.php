<?php
namespace DDL\Hosts;

class HandleDebug {
	private $modules = array ();
	private $files = array ();
	
	function addModule($host, $module) {
		$this->modules [$host] = $module;
	}
	function addUpload($file) {
		$this->files [] = $file;
	}
	
	static function OutputUploading($to_run) {
		$links = array ();
		foreach ( $to_run as $f ) {
			if (! isset ( $links [$f ['file']] )) {
				$links [$f ['file']] = array ();
			}
			$links [$f ['file']] [] = $f ['host'];
		}
		
		foreach ( $links as $file => $hosts ) {
			echo basename ( $file ), " to ", implode ( ',', $hosts ), "\r\n";
		}
	}
	
	function Execute() {
		$to_run = array (); //The run queue, should contain modules * files instances.
		$links = array (); //Links returned
		
		//Create run queue
		foreach ( $this->files as $fn => $f ) {
			foreach ( $this->modules as $host => $module ) {
				$links[$host] = array();
				$to_run [] = array ('module' => $module, 'host' => $host, 'file' => $f, 'file_no' => $fn, 'try_count' => 0 );
			}
		}

		//Output Info		
		echo "\r\n";
		echo "[Uploader] Upload attempt *debug*, Uploading ", count ( $to_run ), " instances\r\n";
		echo "Uploading:\r\n";
		self::OutputUploading ( $to_run );
		echo "\r\n";
		
		//Execute Syncronously
		foreach($to_run as $r){
			$return = $r['module']->Upload($r['file']);
			curl_setopt ( $return->CH (), CURLOPT_NOPROGRESS, false );
			$data = curl_exec($return->CH ());
			$links[$r['host']][] = $return->Callback($data);
		}
		
		//Return links
		return $links;
	}
}
?>