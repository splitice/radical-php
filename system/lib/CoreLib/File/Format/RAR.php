<?php
namespace File\Format;

class RAR {
	private $src;
	private $to;
	private $password;
	private $split = -1;
	private $compress_level = null;
	private $basedir = null;
	private $basedir_recover = null;
	private $comment = null;
	
	function __construct($src, $to) {
		$this->src = $src;
		$this->to = $to;
	}
	function setPassword($password) {
		$this->password = $password;
	}
	static function filesize2bytes($str) {
		$bytes = 0;
		
		$bytes_array = array ('B' => 1, 'KB' => 1024, 'MB' => 1024 * 1024, 'GB' => 1024 * 1024 * 1024, 'TB' => 1024 * 1024 * 1024 * 1024, 'PB' => 1024 * 1024 * 1024 * 1024 * 1024 );
		
		$bytes = floatval ( $str );
		
		if (preg_match ( '#([KMGTP]?B)$#si', $str, $matches ) && ! empty ( $bytes_array [$matches [1]] )) {
			$bytes *= $bytes_array [$matches [1]];
		}
		
		$bytes = intval ( round ( $bytes, 2 ) );
		
		return $bytes;
	}
	function setSplit($size) {
		if (! is_integer ( $size )) {
			$size = self::filesize2bytes($size);
		}
		$this->split = $size;
	}
	function setCompression($lv){
		$this->compress_level = $lv;
	}
	function setBaseDir($dir){
		$this->basedir = $dir;
		$this->basedir_recover = getcwd();
	}
	function setComment($comment){
		global $_CONFIG;
		$temp_file = $_CONFIG ['rar'] ['temp_directory'].'/'.md5($comment.'_rar_comment').'.txt';
		file_put_contents($temp_file, $comment);
		$this->comment = $temp_file;
	}
	
	function Compress(){
		global $_CONFIG;
		
		$cmd = 'rar a ';
		
		//Build Command
		$cmd .= '-y -o+ ';
		if($this->password){
			$cmd .= ' -p'.escapeshellarg($this->password);
		}
		if($this->split){
			$cmd .= ' -v'.$this->split.'b';
		}
		if($this->compress_level!==null){
			$cmd .= ' -m'.$this->compress_level;
		}
		if($this->basedir){
			$cmd .= ' -ep';
		}
		if($this->comment){
			$cmd .= ' -z'.escapeshellarg($this->comment);
		}
		
		//Paths
		if(is_array($this->src) && count($this->src)>1){
			foreach($this->src as $k=>$s){
				$this->src[$k] = realpath($s);
			}
			
			//Multiple Files
			$temp_file = $_CONFIG ['rar'] ['temp_directory'].'/'.md5(time().rand(0,1000));
			file_put_contents($temp_file, implode("\r\n",$this->src));
			
			$cmd .= ' '.escapeshellarg($this->to).' @'.escapeshellarg($temp_file);
		}elseif(is_array($this->src)){
			$this->src = array_values($this->src);
			$cmd .= ' '.escapeshellarg($this->to).' '.escapeshellarg($this->src[0]);
		}else{
			//Single File
			$cmd .= ' '.escapeshellarg($this->to).' '.escapeshellarg($this->src);
		}
		
		//remove if exists
		if(file_exists($this->to)) {
			unlink($this->to);
		}
		
		//Set basedir
		if($this->basedir){
			if(!is_dir($this->basedir)){
				throw new \Exception('Basedir not valid: '.$this->basedir);
			}
			chdir($this->basedir);
		}
		
		//Execute Command
		//echo "RAR: $cmd\r\n";
		$e = new \CLI\Process\Execute ($cmd);
		$process = $e->Run();
		do {
			\CLI\Thread::$self->Sleep(1);
		}while($process->isRunning());
		
		//Clear temp files
		if(isset($temp_file)){
			unlink($temp_file);
		}
		if($this->comment){
			unlink($this->comment);
		}
		
		//Recover basedir
		if($this->basedir_recover){
			chdir($this->basedir_recover);
		}
		
		//Return created rar files
		return glob(dirname($this->to).'/*');
	}
	
	/* SIMPLE UNRAR */
	static function unRAR($in, $to) {
		$to = realpath($to);
		
		//Setup Destination
		if (file_exists ( $to )) {
			\Folder::Remove ( $to );
		}
		@mkdir ( $to );
		
		//Execute
		$e = new \CLI\Process\Execute ( 'rar -y -o+ e ' . escapeshellarg ( $in ) . ' ' . escapeshellarg ( $to ) );
		$process = $e->Run();
		
		do {
			\CLI\Thread::$self->Sleep(1);
		}while($process->isRunning());
	}
}