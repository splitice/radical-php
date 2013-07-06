<?php
namespace Web\Page\Router\Recognisers;

use Utility\Net\URL;
use Web\Page\Router\IPageRecognise;
use Web\Page\Controller;
use Web\Page\Handler;

class API implements IPageRecognise {
	const DEFAULT_TYPE = 'json';
	static function error($string,$type){
		return Handler::Objectify ( 'API', array('error'=>$string,'type'=>$type) );
	}
	static function recognise(URL $url){
		$url = $url->getPath();
		if($url->firstPathElement() == 'api'){
			$url->removeFirstPathElement();
			$module = $url->firstPathElement();
			$url->removeFirstPathElement();
			$method = $url->firstPathElement();
			$url->removeFirstPathElement();
			$type = static::DEFAULT_TYPE;
			
			if(count($parts = explode('.',$method)) > 1){
				if(count($parts) != 2){
					throw new \Exception('Invalid API Method');
				}
				$method = $parts[0];
				$type = $parts[1];
			}
			
			//Check Class
			$c = '\\Web\\Page\\API\\Module\\'.$module;
			if(!class_exists($c)){
				return static::Error('Invalid Module',$type);
			}
			
			//Check Type
			switch($type){
				case 'json':
				case 'xml':
				case 'ps':
					break;
					
				default:
					if(!$c::canType($type)){
						throw new \Exception('Invalid API type: '.$type);
					}
					break;
			}
			
			//Method
			if(isset($_SERVER['HTTP_CONTENT_TYPE']) && $_SERVER['HTTP_CONTENT_TYPE'] == 'application/octet-stream'){
				$query = $url->getQuery();
			}else{
				if($url->getQuery()){
					$query = array_merge($url->getQuery(),$_POST);
				}else{
					$query = $_POST;
				}
				}
			
			$c = new $c($query,$type);
			if($c->can($method)){
				return Handler::Objectify ( 'API', array('object'=>$c,'method'=>$method, 'type'=>$type) );
			}else{
				return static::Error('Invalid Method',$type);
			}
		}
	}
}