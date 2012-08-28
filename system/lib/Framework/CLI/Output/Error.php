<?php
namespace CLI\Output;
use Core\ErrorHandling\Handler;

class Error extends Internal\OutputBase {
	const FORMAT = "[%s] %s\r\n";
	
	static $ERROR_LEVEL = E_ALL;
	static function init(){
		//self::$ERROR_LEVEL = E_ALL & ~E_NOTICE & ~E_WARNING;
	}
	static function output($code, $str, $error){
		$str = static::E($str);
		
		if(self::$ERROR_LEVEL & $error){
			$errorHandler = Handler::getInstance();
			if($errorHandler->isNull()){
				$str = sprintf(static::FORMAT,$code,$str);
				parent::Output($str);
			}else{
				new Error\OutputError($str, $code);
			}
		}
	}
	static function notice($str){
		static::Output('NOTICE',$str,E_NOTICE);
	}
	static function warning($str){
		static::Output('WARN',$str,E_NOTICE);
	}
	static function error($str){
		static::Output('ERROR',$str,E_NOTICE);
	}
	static function fatal($str,$exit=true){
		static::Output('FATAL',$str,E_NOTICE);
		if($exit){
			exit;
		}
	}
}
Error::Init();