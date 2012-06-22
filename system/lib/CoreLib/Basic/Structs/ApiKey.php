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
	public function getDetails(detail = null) {
		if ($detail = 'key') return $this->key;
		else return array('key' => $this->key);
	}
}