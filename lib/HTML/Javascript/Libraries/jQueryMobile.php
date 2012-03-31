<?php
namespace HTML\Javascript\Libraries;
use HTML\Shared;

class jQueryMobile extends Shared\LibraryBase {
	const URL = 'http://code.jquery.com/mobile/%(version)s/jquery.mobile-%(version)s.min.js';
	
	function __construct($version = 1){
		$version = $version ? $version : 1;
		parent::__construct($version);
	}
}