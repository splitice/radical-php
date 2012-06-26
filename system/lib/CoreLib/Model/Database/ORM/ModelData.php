<?php
namespace Model\Database\ORM;

class ModelData {
	public $table;
	public $tableInfo;
	public $mappings;
	public $reverseMappings;
	public $fields;
	public $relations = array();//$this -> $other
	public $references = array();//$other -> $this
	public $id;
	public $validation;
	public $engine;
	public $dynamicTyping;
	public $autoIncrementField;
	public $autoIncrement;
	
	function __construct(array $mappings){
		$this->reverseMappings = array_flip($mappings);
	}
}