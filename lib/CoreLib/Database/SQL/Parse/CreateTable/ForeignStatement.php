<?php
namespace Database\SQL\Parse\CreateTable;

class ForeignStatement extends Internal\CreateTableStatementBase {
	const REFERENCE_REGEX = '#^REFERENCES\s+`([^`]+)`\s+\(`([^`]+)`\)#is';
	protected $reference;
	protected $field;
	
	function __construct($name, $field, $type, $attributes){
		//REFERENCES `grower` (`grower_id`)
		$this->field = $field;
		if(preg_match(static::REFERENCE_REGEX, $attributes, $m)){
			$this->reference = new ColumnReference($m[1], $m[2]);
			$attributes = ltrim(preg_replace(static::REFERENCE_REGEX,'',$attributes));
		}
		parent::__construct($name,$type,$attributes);
	}
	
	/**
	 * @return the $reference
	 */
	public function getReference() {
		return $this->reference;
	}
	/**
	 * @return the $field
	 */
	public function getField() {
		return $this->field;
	}

}