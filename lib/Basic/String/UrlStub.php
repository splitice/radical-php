<?php
namespace Basic\String;

class UrlStub extends \Core\Object {
	function __construct($title){
		$this->title = $title;
	}
	
	function Filter(){
		//Split it into words
		$seoit = explode(' ',mb_strtolower($this->title,'UTF-8'));
		
		//Remove any possible HTML tag characters, make strng and remove blank words
		$seoit = str_replace(array('>','<'),' ',implode(" ", array_filter($seoit)));
		
		//Remove non word characters and URLify (-)
		return trim(preg_replace('#\W+#', "-", str_replace("'", "", $seoit)), "-.");
	}
}