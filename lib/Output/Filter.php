<?php
namespace Output;

class Filter {
	private $function;
	function __construct($function){
		$this->function = $function;
	}
	
	function __invoke(){
		return call_user_func_array($this->function, func_get_args());
	}
	
	static $filters = array();
	static function Register(Filter $filter){
		if(!static::$filters){
			ob_start(array(get_called_class(),'obFilter'));
		}
		static::$filters[] = $filter;
	}
	static function deRegister(Filter $filter){
		foreach(static::$filters as $k=>$f){
			if($f == $filter){
				unset(static::$filters[$k]);
			}
		}
		if(!static::$filters){
			@ob_end_clean();
		}
	}
	
	static function obFilter($in){
		foreach(self::$filters as $f){
			$in = $f($in);
		}
		return $in;
	}
}
