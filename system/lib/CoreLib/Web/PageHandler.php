<?php
namespace Web;

class PageHandler extends \Core\Object {
	static $__dependencies = array('interface.Web.PageHandler.IPage','interface.Web.PageHandler.IPage');
	
	static $stack;
	
	static function Init(){
		if(!static::$stack){
			static::$stack = new \SplStack();
		}
	}
	static function __callStatic($method,$arguments){
		static::Init();
		return call_user_func_array(array(static::$stack,$method),$arguments);
	}
	
	static function Objectify($object,$data = null){
		$class = '\\Web\\Pages\\'.$object;
		return new $class($data);
	}
}