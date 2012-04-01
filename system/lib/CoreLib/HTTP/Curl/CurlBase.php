<?php
namespace HTTP\Curl;
use Basic\ArrayLib\Object\CollectionObject;

abstract class CurlBase extends CollectionObject {
	function setUrl($url){
		$this->data[CURLOPT_URL] = $url;
	}
}