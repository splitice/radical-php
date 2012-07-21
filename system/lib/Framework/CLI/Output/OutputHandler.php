<?php
namespace CLI\Output;

class OutputHandler {
	private static $handler;
	
	static function Output($string){
		if(self::$handler instanceof Handler\IOutputHandler){
			self::$handler->Output($string);
		}
	}
	
	static function setHandler(Handler\IOutputHandler $handler){
		static::$handler = $handler;
	}
	
	static function Init(){
		self::$handler = new Handler\EchoOutput();
	}
}
OutputHandler::Init();