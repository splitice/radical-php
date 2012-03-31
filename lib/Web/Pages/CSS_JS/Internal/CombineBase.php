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
			$files = glob(__DIR__.DS.'..'.DS.'..'.DS.'..'.DS.'css'.DS.$name.DS.'*.css');
			foreach($files as $f){
				$version = max($version,filemtime($f));
			}
			$cache->Set($name, $version, 10);
		}
		
		return '/'.$name.'.'.$version.'.'.static::EXTENSION;
	}
	private function getFiles(){
		$path = $this->getPath();
		return glob($path.DS.'*.'.static::EXTENSION);
	}
	protected function sendHeaders(){
		parent::sendHeaders();
		$headers = \Web\PageHandler::top()->headers;
		//$headers->setCache('public, max-age='.(60*60*24*7));
		//$headers->setExpires(strtotime('+1 week'));
		
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
				$f = basename($f);
				$url = \Net\URL::fromRequest('/'.static::EXTENSION.'/'.$this->name.'/'.$f);
				$data[$f] = \Web\PageHandler\SubRequest::fromURL($url)->Execute('GET');
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