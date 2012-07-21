<?php
namespace Model\Database\SQL\Parse\Types\Internal;

abstract class TypeBase {
	const TYPE = '';
	static function is($type){
		return (static::TYPE == $type);
	}
	
	protected $type;
	protected $size;
	protected $null = false;
	function __construct($type,$size){
		$this->type = $type;
		$this->size = $size;
	}
	
	function canNull($null){
		$this->null = $null;
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
	
	function _Validate($value){
		if($value === null && $this->null) return true;
	}
}