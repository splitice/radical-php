<?php
namespace Net\ExternalInterfaces\ContentAPI\Modules\Structs;

class CastMember {
	private $name;
	private $character;
	private $as;
	
	function __construct($name,$character = null,$as = null){
		$this->name = $name;
		$this->character = $name;
		$this->as = $as;
	}
	
	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return the $character
	 */
	public function getCharacter() {
		return $this->character;
	}

	/**
	 * @return the $as
	 */
	public function getAs() {
		return $this->as;
	}

	function __toString(){
		return $this->name;
	}
}