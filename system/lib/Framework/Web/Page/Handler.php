<?php
namespace Web\Page;

use Web\Page\Handler\NullPageRequest;

class Handler extends \Core\Object {
	static $__dependencies = array('interface.Web.Page.Handler.IPage','interface.Web.Page.Handler.IPage');
	
	/**
	 * A stack of Page\Handlers, used for preserving state (headers etc) during subrequests.
	 * 
	 * @var SplStack
	 */
	static $stack;
	
	static function init(){
		if(!static::$stack){
			static::$stack = new \SplStack();
		}
	}
	static function __callStatic($method,$arguments){
		static::Init();
		return call_user_func_array(array(static::$stack,$method),$arguments);
	}
	
	static function top(){
		return self::current();
	}
	
	/**
	 * @param bool $notExistsCreate
	 * @return \Web\Page\Handler\NullPageRequest
	 */
	static function current($notExistsCreate = false){
		static::Init();
		if(static::$stack->count() == 0){
			if($notExistsCreate){
				return new NullPageRequest();
			}
			return null;
		}else{
			return static::$stack->top();
		}
	}
	
	static function objectify($object,$data = null){
		$class = '\\Web\\Page\\Controller\\'.$object;
		return new $class($data);
	}
}