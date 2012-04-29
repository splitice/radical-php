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
		return $this->name;
	}
	private function getFile(){
		//TODO: Override
		global $BASEPATH;
		$expr = $BASEPATH.'*'.DS.$this->getPath();
		return array_pop(glob($expr));
	}
	function GET(){
		$this->sendHeaders();
		$file = $this->getFile();
		$ret = file_get_contents($file);
		
		echo $ret;
		//return new \PageHandler\GZIP($ret);
	}
}