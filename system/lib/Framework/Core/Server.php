<?php
namespace Core;

/**
 * Get infomation about the server / script deployment
 * 
 * @author SplitIce
 *
 */
class Server {
	/**
	 * Is this site running in a production environment?
	 * 
	 * @return boolean
	 */
	static function isProduction(){
		//TODO: Deployment
		if(!isset($_SERVER['SERVER_ADDR'])) return true;
		return (substr($_SERVER['SERVER_ADDR'],0,4) !== '192.' && $_SERVER['SERVER_ADDR'] !== '::1' && $_SERVER['SERVER_ADDR'] !== '127.0.0.1');
	}
	
	/**
	 * Is this site running in Command Line mode?
	 * 
	 * @return boolean
	 */
	static function isCLI(){
		return (PHP_SAPI === 'cli');
	}
	
	/**
	 * Is this script running on windows?
	 * 
	 * @return boolean
	 */
	static function isWindows(){
		return (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN');
	}
	
	/**
	 * Get the directory root for the site.
	 * 
	 * @return string
	 */
	static function getSiteRoot(){
		global $WEBPATH;
		if(!isset($WEBPATH))
			return '/';
		return $WEBPATH.'/';
	}
}