<?php
namespace Web\Pages\CSS_JS\Internal;

abstract class IndividualBase extends \Web\PageHandler\PageBase {
	protected $name;

	const MIME_TYPE = 'text/plain';
	const EXTENSION = '';
	
	function __construct($data){
		$this->name = $data['name'];
	}
	protected function sendHeaders(){
		$headers = \Web\PageHandler::top()->headers;
		$headers->Add('Content-Type',static::MIME_TYPE);
	}
	protected function getPath(){
		$path = realpath(__DIR__.DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.static::EXTENSION.DS.$this->name);
		return $path;
	}
	function GET(){
		$this->sendHeaders();
		
		$ret = file_get_contents($this->getPath());
		
		echo $ret;
		//return new \PageHandler\GZIP($ret);
	}
}