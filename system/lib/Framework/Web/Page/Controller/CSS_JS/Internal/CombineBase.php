<?php
namespace Web\Page\Controller\CSS_JS\Internal;
use Web\Page\Controller\CSS_JS\CSS\Individual;
use Utility\Cache\PooledCache;

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
	static function link($name){
		$cache = PooledCache::Get(get_called_class(), 'Memory');
		
		$version = (int)$cache->Get($name);
		if(!$version){
			$path = new \Core\Resource(static::EXTENSION.DS.$name);
			foreach($path->getFiles() as $f){
				$version = max($version,filemtime($f));
			}
			$cache->Set($name, $version, 10);
		}
		
		return '/'.$name.'.'.$version.'.'.static::EXTENSION;
	}
	static function exists($name){
		$path = new \Core\Resource(static::EXTENSION.DS.$name);
		return $path->exists();
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
	function optimize($code){
		return $code;
	}
	/**
	 * Handle GET request
	 *
	 * @throws \Exception
	 */
	function GET(){
		$key = static::EXTENSION.'_'.$this->name.'_'.$this->version;
		
		$this->sendHeaders();
		$cache = PooledCache::Get(get_called_class(), 'Memory');
		
		$ret = $cache->get($key);
		
		if(!$ret){
			$data = array();
			$files = $this->getFiles();
			foreach($files as $f){
				if(is_file($f)){//Ignore folders
					//die(var_dump($f));
					$fn = basename($f);
					//$url = \Utility\Net\URL::fromRequest('/'.static::EXTENSION.'/'.$this->name.'/'.$f);
					//$data[$f] = \Web\Page\Handler\SubRequest::fromURL($url)->Execute('GET');
					$data[$fn] = Individual::get_file($f);
				}
			}
			
			$ret = '';
			foreach($data as $f=>$d){
				if(!\Core\Server::isProduction()){
					$ret .= "\r\n/* Including: ".$f." */\r\n";
				}
				$ret .= $d;
			}
			
			if(\Core\Server::isProduction()){
				$ret = $this->Optimize($ret);
				$cache->set($key, $ret);
			}
		}
		
		echo $ret;
		//return new \Page\Handler\GZIP($ret);
		
		$headers = \Web\Page\Handler::top()->headers;
		$headers->setContentLength(strlen($ret));
	}
}