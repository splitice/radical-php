<?php
namespace Database\SQL\Parse\Types\Internal;

abstract class TypeBase {
	const TYPE = '';
	static function is($type){
		return (static::TYPE == $type);
	}
	
	protected $type;
	protected $size;
	function __construct($type,$size){
		$this->type = $type;
		$this->size = $size;
	}
	
	/**
	 * @return the $type
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return the $size
	 */
	public function getSize() {
		return $this->size;
	}
}