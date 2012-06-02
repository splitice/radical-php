<?php
namespace Web\Resource\Libraries;
use Web\Resource\Shared;

class jQuery extends Shared\LibraryBase implements IJavascriptLibrary {
	const URL = 'http://ajax.googleapis.com/ajax/libs/jquery/%(version)s/jquery.min.js';
	
	function __construct($version = 1){
		$version = $version ? $version : 1;
		parent::__construct($version);
	}
}