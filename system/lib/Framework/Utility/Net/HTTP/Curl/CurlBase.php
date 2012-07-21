<?php
namespace Utility\Net\HTTP\Curl;
use Basic\Arr\Object\CollectionObject;

abstract class CurlBase extends CollectionObject {
	function setUrl($url){
		$this->data[CURLOPT_URL] = $url;
	}
}