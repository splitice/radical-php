<?php
namespace Web\API;

abstract class APIBase extends \Core\Object implements IAPIModule {
	protected $data;
	protected $type;
	function __construct($data,$type){
		$this->data = $data;
		$this->type = $type;
	}
	static function canType($type){
		return false;
	}
	function can($method){
		return method_exists($this, $method);
	}
}