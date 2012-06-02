<?php
namespace Web\Resource\Libraries;
use Web\Resource\Shared;

class jQueryMobile extends Shared\LibraryBase implements IJavascriptLibrary {
	const URL = 'http://code.jquery.com/mobile/%(version)s/jquery.mobile-%(version)s.min.js';
	
	function __construct($version = 1.1){
		$version = $version ? $version : 1.1;
		if(is_float($version)){
			$version = (string)$version;
			$version .= '.0';
		}
		parent::__construct($version);
	}
}