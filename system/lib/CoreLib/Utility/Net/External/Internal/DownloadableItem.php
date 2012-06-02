<?php
namespace Net\ExternalInterfaces\Internal;

class DownloadableItem {
	public $link;
	
	function __construct($link){
		$this->link = $link;
	}
	
	private $curl;
	private $data;
	private $filename;
	public function getCurl(){
		if($this->curl){
			return $this->curl;
		}
		$ch = curl_init($this->link);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HEADERFUNCTION,array($this,'readHeader'));
		$this->curl = $ch;
		$this->data = curl_exec($ch);;
		return $ch;
	}
	public function getData(){
		$this->getCurl();
		return $this->data;
	}
	public function getFilename(){
		$this->getCurl();
		return $this->filename;
	}
	private function extractCustomHeader($start,$end,$header) {
		$pattern = '/'. $start .'(.*?)'. $end .'/';
		if (preg_match($pattern, $header, $result)) {
			return $result[1];
		} else {
			return false;
		}
	}
	private function readHeader($ch, $header) {
		//extracting example data: filename from header field Content-Disposition
		$filename = $this->extractCustomHeader('Content-Disposition: attachment; filename=', '\n', $header);
		if ($filename) {
			$this->filename = rtrim(trim($filename),'; ');
		}
		return strlen($header);
	}
}