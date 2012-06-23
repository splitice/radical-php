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
	public function getDetails($detail = null) {
		if ($detail == 'key') return $this->key;
		elseif($detail === null) return array('key' => $this->key);
		throw new \Exception('Invalid Detail');
	}
	/**
	 * @return the $key
	 */
	public function getKey() {
		return $this->key;
	}
	
	/**
	 * Do we have details?
	 *
	 * @return bool true if we have details
	 */
	function hasDetails(){
		return !empty($this->key);
	}
}