<?php
namespace Net\ExternalInterfaces\ContentAPI\Modules\LastFM;

class API {
	private $apiKey;
	function __construct($apiKey = 'b25b959554ed76058ac220b7b2e0a026'){
		$this->apiKey = $apiKey;
	}
	protected function getURL($method,$arguments = array()){
		$url = 'http://ws.audioscrobbler.com/2.0/?method='.urlencode($method).'&api_key='.urlencode($this->apiKey);
		if($arguments) $url .= '&'.http_build_query($arguments);
		return $url;
	}
	function albumGetInfo($artist,$album){
		$url = $this->getURL('album.getinfo',compact('artist','album'));
		$ch = curl_init($url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$data = curl_exec($ch);
		
		$xml = new \SimpleXMLElement($data);
		if($xml['status'] == 'ok'){
			return $xml->album;
		}else{
			return (string)$xml->error;
		}
	}
}