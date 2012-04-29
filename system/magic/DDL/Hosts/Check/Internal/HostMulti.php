<?php
namespace DDL\Hosts\Check\Internal;

class HostMulti {
	private $class;
	private $url;
	private $callback;
	
	function __construct($class,$url,$callback){
		$this->class = $class;
		$this->url = $url;
		$this->callback = $callback;
	}
	
	function Callback($result){
		$status = 'unknown';
		
		if($result->hasContent()){
			$status = call_user_func(array($this->class,'ValidateCheck'),$result->getContent(),$this->url);
		}
		
		if(!($status instanceof CheckReturn)){
			$status = new CheckReturn($status);
		}
		
		call_user_func($this->callback,$status,$this->url);
	}
}