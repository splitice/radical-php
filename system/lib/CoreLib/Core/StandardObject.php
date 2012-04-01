<?php
namespace Core;

abstract class StandardObject extends Object {
	function to($method){
		//Handle common standard methods
		switch($method){
			case 'string':
				return (string)$this;
			
			case $this:
				return $this;
		}
		
		//Handle global
		
		//Handle per-object
		$methodName = 'to'.$method;
		if(method_exists($this, $methodName)){
			return $this->$methodName();
		}
		throw new \Exception('->to doesnt know how to handle "'.$method.'"');
	}
}