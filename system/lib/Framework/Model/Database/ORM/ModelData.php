<?php
namespace Model\Database\ORM;

use Model\Database\SQL\Parse\CreateTable;

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
	
	function getMappings($structure = null){
		//Accept a null strucure for when this method is called externally
		if($structure === null){
			$structure = CreateTable::fromTable($this->table);
		}
		
		//Return a Mapping Manager
		return new Mappings($this,$structure);
	}
}