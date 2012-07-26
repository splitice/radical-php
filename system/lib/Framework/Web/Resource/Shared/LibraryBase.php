<?php
namespace Web\Resource\Shared;
use Basic\String;

abstract class LibraryBase extends \Core\Object {
	const URL = '';
	protected $path;
	public $depends = array();
	
	function __construct($version){
		$this->path = String\Format::sprintfn(static::URL,compact('version'));
	}
	
	function __toString(){
		return $this->path;
	}
}