<?php
namespace Web\Page\Controller\CSS_JS\Internal;

abstract class IndividualBase extends \Web\Page\Handler\PageBase {
	protected $name;

	const MIME_TYPE = 'text/plain';
	const EXTENSION = '';
	
	function __construct($data){
		$this->name = $data['name'];
	}
	protected function sendHeaders($file){
		if(!is_array($file)){
			$file = array($file);
		}
		
		$headers = \Web\Page\Handler::current()->headers;
		$headers->Add('Content-Type',static::MIME_TYPE);
		$headers->setCache(60*60*24);
		$headers->Add('Pragma','cache');
		
		$filemtime = max(array_map('filemtime',$file));
		//die(var_dump($file));
		$headers->setLastModified($filemtime);
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
	/**
	 * Handle GET request
	 *
	 * @throws \Exception
	 */
	function GET(){
		$file = $this->getFile();
		$this->sendHeaders($file);
		$ret = file_get_contents($file);
		
		echo $ret;
		//return new \Page\Handler\GZIP($ret);
	}
}