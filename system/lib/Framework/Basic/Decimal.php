<?php
namespace Basic;

class Decimal {
	protected $value;
	protected $handler;
	
	function __construct($value,$handler = null){
		$this->value = $value;
		if($handler === null){
			if(is_float($value)){//If its a float we have to assume its already approximated
				$handler = new Decimal\Native();
			}elseif(Decimal\GMP::isLoaded()){
				$handler = new Decimal\GMP();
			}else{
				$handler = new Decimal\Native();
			}
		}
		$this->handler = $handler;
	}
	
	function add($a,$b){
		return $this->handler->addition($a,b);
	}
	function sub($a,$b){
		return $this->handler->subtract($a,b);
	}
	function mult($a,$b){
		return $this->handler->multiply($a,b);
	}
	function mul($a,$b){
		return $this->handler->multiply($a,b);
	}
	function div($a,$b){
		return $this->handler->divide($a,b);
	}
	
	function __call($method,$args){
		return call_user_func_array(array($this->handler,$method), $args);
	}
}