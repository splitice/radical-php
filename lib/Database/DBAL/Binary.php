<?php
namespace Database\DBAL;

class Binary {
	private $data;
	
	function __construct($data){
		$s='';
		foreach(explode("\n",trim(chunk_split($data,2))) as $h) $s.=chr(hexdec($h));
		
		$this->data = (string)$s;
	}
	function __toString(){
		return $this->data;
	}
	function toEscaped(){
		return '0x'.bin2hex($this->data);
	}
}