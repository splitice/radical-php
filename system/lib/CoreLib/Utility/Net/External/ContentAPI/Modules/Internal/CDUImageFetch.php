<?php
namespace Net\ExternalInterfaces\ContentAPI\Modules\Internal;

class CDUImageFetch implements \Image\Interfaces\IFetch {
	private $ch;
	
	function __construct($ch){
		$this->ch = $ch;
	}
	function Fetch(){
		$data = curl_exec($this->ch);
		$ch = $this->ch;
		\HTML\Simple_HTML_DOM::LoadS ();
		$dom = \HTML\str_get_dom ( $data );
		
		if($img = $dom->find('img[src*="cover"]',0)){
			$link = $img->src;
			$link = str_replace('/Medium/','/Large/',$link);
			curl_setopt($ch, CURLOPT_URL, $link);
			return curl_exec($ch);
		}
		return;
	}
}