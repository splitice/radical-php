<?php
namespace HTML\Javascript\Libraries;
use HTML\Shared;

class jQuery extends Shared\LibraryBase {
	const URL = 'http://ajax.googleapis.com/ajax/libs/jquery/%(version)s/jquery.min.js';
	
	function __construct($version = 1){
		$version = $version ? $version : 1;
		parent::__construct($version);
	}
}