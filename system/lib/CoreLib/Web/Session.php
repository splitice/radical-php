<?php
namespace Web;

use Web\Session\Storage\Internal;

use Web\Session\Authenticator;
use Web\Session\Authentication\Source\ISessionSource;
use Web\Session\Authentication\IAuthenticator;
use Web\Session\Extra\ISessionExtra;
use Web\Session\Storage\ISessionStorage;

class Session extends \Core\Object {
	static $__dependencies = array(
			'interface.Web.Session.Handler.Internal.ISessionHandler',
			'interface.Web.Session.Extra.Interfaces.ISessionExtra',
		);
	
	const DEFAULT_IP = '::1';
	
	/**
	 * @var Web\Session\Storage\ISessionStorage
	 */
	public static $data;
	/**
	 * @var Web\Session\Authenticator
	 */
	
	public static $auth;
	private static $extras = array();
	
	static function IP(){
		if(\Server::isCLI()){
			return new \Net\IP(static::DEFAULT_IP);
		}
		if(isset($_SERVER['HTTP_X_REAL_IP'])){
			return new \Net\IP($_SERVER['HTTP_X_REAL_IP']);
		}
		if(isset($_SERVER['REMOTE_ADDR'])){
			return new \Net\IP($_SERVER['REMOTE_ADDR']);
		}
		return new \Net\IP(static::DEFAULT_IP);
	}
	
	static function Init(){
		//Create the session authentication controller and internal data storage
		if(!static::$auth){
			static::$auth = new Authenticator();
			static::$data = new Internal();
		}
		
		//Initialize supplied modules
		foreach(func_get_args() as $arg){
			if($arg instanceof ISessionStorage){
				static::$data = $arg;
			}elseif($arg instanceof ISessionExtra){
				static::$extra[get_class($arg)] = $arg;
			}elseif($arg instanceof IAuthenticator){
				static::$auth->setAuthenticator($arg);
			}elseif($arg instanceof ISessionSource){
				static::$auth->setSource($arg);
			}
		}
	}
	
}