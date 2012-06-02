<?php
namespace Web;

use Web\Page\Handler\SubRequest;

abstract class Widget {
	protected $vars;
	function __construct(array $vars){
		$this->vars = $vars;
	}
	
	abstract function Render();
	
	function __toString(){
		try {
		$t = $this->Render();
		if($t !== null){
			$sub = new SubRequest($t);
			$resp = $sub->Execute();
			return $resp;
		}
		}catch(\Exception $ex){
			return 'An error occurred in the widget '.get_called_class().': '.$ex->getMessage();
		}
	}
	
	static function Load($name,array $vars){
		if($name{0} != '\\'){
			$name = '\\Web\\Widgets\\'.$name;
		}
		return new $name($vars);
	}
}