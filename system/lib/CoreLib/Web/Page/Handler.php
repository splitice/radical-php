<?php
namespace Web\Page;

use Web\Page\Handler\NullPageRequest;

class Handler extends \Core\Object {
	static $__dependencies = array('interface.Web.Page\Handler.IPage','interface.Web.Page\Handler.IPage');
	
	/**
	 * A stack of Page\Handlers, used for preserving state (headers etc) during subrequests.
	 * 
	 * @var SplStack
	 */
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
	
	/**
	 * @param bool $notExistsCreate
	 * @return \Web\Page\Handler\NullPageRequest
	 */
	static function current($notExistsCreate = false){
		$ret = static::$stack->top();
		if($notExistsCreate && !$ret){
			$ret = new NullPageRequest();
		}
		return $ret;
	}
	
	static function Objectify($object,$data = null){
		$class = '\\Web\\Pages\\'.$object;
		return new $class($data);
	}
}