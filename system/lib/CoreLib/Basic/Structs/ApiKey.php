<?php
namespace Basic\Structs;

class ApiKey implements ILoginDetails {
	protected $key;
	
	function __construct($key){
		$this->key = $key;
	}
	
	/**
	 * @return the $key
	 */
	public function getDetails() {
		return array($this->key);
	}
}