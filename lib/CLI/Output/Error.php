<?php
namespace CLI\Output;
use ErrorHandling\Handler;

class Error extends Internal\OutputBase {
	const FORMAT = "[%s] %s\r\n";
	
	static $ERROR_LEVEL = E_ALL;
	static function Init(){
		//self::$ERROR_LEVEL = E_ALL & ~E_NOTICE & ~E_WARNING;
	}
	static function Output($code, $str, $error){
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
	static function Notice($str){
		static::Output('NOTICE',$str,E_NOTICE);
	}
	static function Warning($str){
		static::Output('WARN',$str,E_NOTICE);
	}
	static function Error($str){
		static::Output('ERROR',$str,E_NOTICE);
	}
	static function Fatal($str,$exit=true){
		static::Output('FATAL',$str,E_NOTICE);
		if($exit){
			exit;
		}
	}
}
Error::Init();