<?php
namespace Basic\Weakref;

class Callback extends \Core\Object {	
	private $method;
	private $failure;
	
	function __construct($object,$method,$failure = Failure::NOTHING){
		$this->object = new \Weakref($object);
		$this->method = $method;
		$this->failure = $failure;
	}
	
	function call(){
		if($this->object->valid()){
			$object = $this->object->get();
			$method = $this->method;
			
			return $object->$method();
		}
		
	}
	
	static function callback($object, $method,$failure = Failure::NOTHING){
		$object = new static($object,$method,$failure);
		return array($object,'Call');
	}
}