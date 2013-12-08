<?php
namespace Web\Page\Router\Recognisers;

use Utility\Net\URL;
use Web\Page\Router\Recognise;
use Web\Page\Router\IPageRecognise;
use Utility\Net\HTTP;
use Web\Page\Controller;
use Model\Database\DBAL\Handler\QueryLog;

class AAAA_Debug implements IPageRecognise {
	private static $enable = true;
	
	private static function _call(URL $url){
		//Execute origional page
		ob_start();
		$handler = Recognise::fromURL($url);
		if($handler){
			$handler->Execute($_SERVER['REQUEST_METHOD']);
		}
		ob_end_clean();
	}
	static function recognise(URL $url){
		//Just incase disabling debug doesnt work.
		if(!self::$enable){
			return;
		}
		self::$enable = false;
		
		$query = $url->getPath()->getQuery();
		if(isset($query['XDEBUG_PROFILE'])){
			ob_start(function(){
				return xdebug_get_profiler_filename();
			},0);
		}
		if(isset($query['debug'])){
			$mode = $query['debug'];
			
			//Remove Explain
			unset($query['debug']);
			$url->getPath()->setQuery($query);
			
			//Debug Wrapper
			switch($mode){
				case 'webgrind':
					return new Controller\Debug\Profile(isset($_GET['dataFile'])?(string)$_GET['dataFile']:null);
					break;
				
				case 'profile':
					//Build new URL
					$new_url = clone $url;
					$query = $url->getPath()->getQuery();
					$query['XDEBUG_PROFILE'] = '1';
					$new_url->getRealPath()->setQuery($query);
					$host = $new_url->getHost();
					$new_url->setHost($_SERVER['SERVER_ADDR']);

					//Execute debug request
					$http = new HTTP\Fetch((string)$new_url);
					$http->setHeader('Host',$host);
					
					if(isset($_SERVER["PHP_AUTH_USER"])){
						$http->curl[CURLOPT_USERPWD] = $_SERVER["PHP_AUTH_USER"].':'.$_SERVER["PHP_AUTH_PW"];
						$http->curl[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
					}
					
					if(isset($_COOKIE[session_name()])){
						$http->curl[CURLOPT_COOKIE] = session_name().'='.$_COOKIE[session_name()];
					}
					if($_SERVER['REQUEST_METHOD'] == "POST"){
						$r = $http->post(http_build_query($_POST));
					}else{
						$r = $http->Execute();
					}
					
					//Execute WebGrind
					$filename = /*'cachegrind.out.5503';//*/basename((string)$r);
					return new Controller\Debug\Profile($filename);
					break;
					
				case 'sql':
					\DB::$query_log->explain = true;
					ob_start(function(){
						$sql = '';
						foreach(\DB::$query_log->getQueries() as $q){
							$sql .= $q.'<br />';
						}
						
						return $sql;
					});
					break;
					
				case 'explain':
					break;
			}
		}
	}
}