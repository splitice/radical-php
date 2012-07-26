<?php
namespace Web\Resource\Javascript\Libraries;
use Web\Resource\Shared;

class jQueryMobile extends Shared\LibraryBase implements IJavascriptLibrary {
	const URL = 'http://ajax.aspnetcdn.com/ajax/jquery.mobile/%(version)s/jquery.mobile-%(version)s.min.js';
	
	function __construct($version = 1.1){
		$version = $version ? $version : 1.1;
		if(is_float($version)){
			$version = (string)$version;
			$version .= '.0';
		}
		$this->depends['jquery'] = 'jquery';
		parent::__construct($version);
	}
}