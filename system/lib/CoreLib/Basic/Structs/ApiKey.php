<?php
namespace Basic\Structs;

class ApiKey extends LoginDetails {
	protected $key;
	
	function __construct($key){
		$this->key = $key;
	}
	
	/**
	 * @return the $key
	 */
	public function getKey() {
		return $this->key;
	}
}