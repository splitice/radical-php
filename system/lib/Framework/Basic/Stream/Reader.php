<?php
namespace Basic\Stream;

/**
 * A class providing a StreamReader interface
 * 
 * @author SplitIce
 */
class Reader {
	private $stream;
	private $chunk_size;
	
	function __construct($stream,$chunk_size = 4096){
		$this->stream = $stream;
		$this->chunk_size = $chunk_size;
	}
	
	function ReadAll(){
		stream_set_blocking($this->stream, false);
		
		$ret = '';
		while(!feof($this->stream)){
			$wait = array($this->stream);
			$null = null;
			stream_select($wait,$null,$null,3,0);
			$ret .= fread($this->stream,$this->chunk_size);
		}
		
		stream_set_blocking($this->stream, true);
		
		return $ret;
	}
}