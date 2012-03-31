<?php
namespace Video;

class Screenshot {
	const TYPE_LARGE = 1;
	const TYPE_COMBINED = 2;
	
	private $file;
	private $type;
	private $font;
	private $min_height = 100;
	private $skip_end = 600;
	private $skip_begin = 10;
	private $rows = 7;
	private $cols = 4;
	private $edge_detection = 0;
	private $out = '/tmp/';
	private $timestamp = true;
	private $text;
	private $width = null;
	
	function __construct($file, $type = self::TYPE_COMBINED) {
		$this->file = $file;
		$this->type = $type;
	}
	function setFont($font) {
		$this->font = $font;
	}
	function setMinHeight($h = 100) {
		$this->min_height = $h;
	}
	function setSkipEnd($seconds) {
		$this->skip_end = $seconds;
	}
	function setSkipBegin($seconds) {
		$this->skip_begin = $seconds;
	}
	function setRows($rows) {
		$this->rows = $rows;
	}
	function setColumns($cols){
		$this->cols = $cols;
	}
	function setOutputDir($out) {
		$this->out = $out;
	}
	function setEdgeDetection($lv){
		$this->edge_detection = (int)$lv;
	}
	function setTimestamp($v){
		$this->timestamp = (bool)$v;
	}
	function setText($t){
		$this->text = $t;
	}
	function setWidth($w){
		$this->width = $w;
	}
	
	function Execute() {
		global $_CONFIG;
		
		//Build CMD with options
		$cmd = $_CONFIG ['screenshot'] ['mtn_dir'] . '/mtn -f ' . escapeshellarg ( $this->font );
		if($this->width  !== null){
			$cmd .= ' -w'.$this->width;
		}
		$cmd .= ' -h ' . $this->min_height . ' -E ' . $this->skip_end . ' -B ' . $this->skip_begin;
		$cmd .= ' -O ' . escapeshellcmd ( $this->out );
		if($this->edge_detection){
			$cmd .= ' -D '.$this->edge_detection;
		}
		if(!$this->timestamp){
			$cmd .= ' -t';
		}
		if ($this->type == self::TYPE_LARGE) {
			$cmd .= ' -I';
		}
		if($this->text){
			$cmd .= ' -T '.escapeshellarg($this->text);
		}
		
		//Advanced options
		$cmd .= ' -r ' . $this->rows;
		
		//File to screenshot -> CMD
		$cmd .= ' ' . escapeshellarg ( $this->file );
		
		//STDERR -> STDOUT
		$cmd .= ' 2>&1';
		
		//echo "Executing: ",$cmd,"\r\n";
		//Execute
		$e = new \CLI\Process\Execute ($cmd);
		$process = $e->Run();
		do {
			\CLI\Thread::$self->Sleep(1);
		}while($process->isRunning());
		
		//Get screenshots generated
		if ($this->type == self::TYPE_COMBINED) {
			$expr = $this->out . '*_s.jpg';
			$r = glob ( $expr );
			if(!$r){
				return array();
			}
			return array (self::I ( $r [0] ) );
		} elseif ($this->type == self::TYPE_LARGE) {
			$r = glob ( $this->out . '*.jpg' );
			foreach ( $r as $k => $v ) {
				if (substr ( $v, - 6 ) == '_s.jpg') {
					unset ( $r [$k] );
				} else {
					$r [$k] = self::I ( $v );
				}
			}
			return $r;
		}
	}
	
	static function I($img) {
		return new \Image\File ( $img );
	}
}