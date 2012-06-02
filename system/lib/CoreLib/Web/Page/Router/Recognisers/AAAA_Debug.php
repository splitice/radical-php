<?php
namespace Web\Page\Router\Recognisers;

use Web\Page\Router\Recognise;
use Web\Page\Router\IPageRecognise;
use Utility\Net\HTTP;
use Web\Page\Controller;

class AAAA_Debug implements IPageRecognise {
	private static $enable = true;
	
	private static function _call(\Net\URL $url){
		//Execute origional page
		ob_start();
		$handler = Recognise::fromURL($url);
		if($handler){
			$handler->Execute($_SERVER['REQUEST_METHOD']);
		}
		ob_end_clean();
	}
	static function Recognise(\Net\URL $url){
		//Just incase disabling debug doesnt work.
		if(!self::$enable){
			return;
		}
		self::$enable = false;
		
		$query = $url->getPath()->getQuery();
		if(isset($query['XDEBUG_PROFILE'])){
			ob_start(function(){
				return xdebug_get_profiler_filename();
			});
		}
		if(isset($query['debug'])){
			$mode = $query['debug'];
			
			//Remove Explain
			unset($query['debug']);
			$url->getPath()->setQuery($query);
			
			//Debug Wrapper
			switch($mode){
				case 'webgrind':
					return new Controller\Debug\Profile((string)$_GET['dataFile']);
					break;
				
				case 'profile':
					//Build new URL
					$new_url = clone $url;
					$query['XDEBUG_PROFILE'] = '1';
					$new_url->getPath()->setQuery($query);
					$host = $new_url->getHost();
					$new_url->setHost($_SERVER['SERVER_ADDR']);
					
					//Execute debug request
					$http = new HTTP\Fetch((string)$new_url);
					$http->setHeader('Host',$host);
					$r = $http->Execute();
					
					//Execute WebGrind
					$filename = /*'cachegrind.out.5503';//*/basename((string)$r);
					return new Controller\Debug\Profile($filename);
					break;
					
				case 'sql':
					break;
					
				case 'explain':
					break;
			}
		}
	}
}