<?php
namespace Web\Pages\CSS_JS\Internal;

abstract class CombineBase extends IndividualBase {
	protected $version;
	
	const EXTENSION = '';
	const MIME_TYPE = 'text/plain';
	
	function __construct($data){
		parent::__construct($data);
		$n = $data['name'];
		if($pos = strrpos($n,'.')){
			$this->version = ((int)substr($n,$pos+1))^6;
			$n = substr($n,0,$pos);
		}
		$this->name = $n;
	}
	static function Link($name){
		$cache = \Cache\PooledCache::Get(get_called_class(), 'Memory');
		
		$version = (int)$cache->Get($name);
		if(!$version){
			$path = new \Core\Resource('css'.DS.$name);
			foreach($path->getFiles('*.css') as $f){
				$version = max($version,filemtime($f));
			}
			$cache->Set($name, $version, 10);
		}
		
		return '/'.$name.'.'.$version.'.'.static::EXTENSION;
	}
	protected function getPath(){
		return static::EXTENSION.DS.parent::getPath();
	}
	private function getFiles($expr = '*'){
		$path = new \Core\Resource($this->getPath());
		return $path->getFiles($expr);
	}
	protected function sendHeaders(){
		parent::sendHeaders($this->getFiles());
	}
	function Optimize($code){
		return $code;
	}
	function GET(){
		$key = static::EXTENSION.'_'.$this->name.'_'.$this->version;
		
		$this->sendHeaders();
		$cache = \Cache\PooledCache::Get(get_called_class(), 'Memory');
		
		$ret = $cache->get($key);
		
		if(!$ret){
			$data = array();
			$files = $this->getFiles();
			foreach($files as $f){
				//die(var_dump($f));
				$fn = basename($f);
				//$url = \Net\URL::fromRequest('/'.static::EXTENSION.'/'.$this->name.'/'.$f);
				//$data[$f] = \Web\PageHandler\SubRequest::fromURL($url)->Execute('GET');
				$data[$fn] = file_get_contents($f);
			}
			
			$ret = '';
			foreach($data as $f=>$d){
				if(!\Server::isProduction()){
					$ret .= "\r\n/* Including: ".$f." */\r\n";
				}
				$ret .= $d;
			}
			if(\Server::isProduction()){
				$ret = $this->Optimize($ret);
				$cache->set($key, $ret);
			}
		}
		
		echo $ret;
		//return new \PageHandler\GZIP($ret);
		
		$headers = \Web\PageHandler::top()->headers;
		$headers->setContentLength(strlen($ret));
	}
}