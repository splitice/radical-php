<?php
namespace Model\Database\ORM;

use Model\Database\Model\TableReference;
use Model\Database\Model\TableReferenceInstance;
use Model\Database\SQL\Parse\CreateTable;

class Model extends ModelData {	
	function __construct(TableReferenceInstance $table){
		$this->table = $table;
		$this->tableInfo = $table->Info();
		$structure = CreateTable::fromTable($table);
		$this->engine = $structure->engine;
		
		//Work out which fields are IDs
		if(isset($structure->indexes['PRIMARY'])){
			$this->id = $structure->indexes['PRIMARY']->getKeys();
		}
		
		//Build mapping translation array
		$this->mappings = $this->getMappings($structure)->translationArray();
		
		//This is the auto increment field, if it exists
		foreach($this->id as $col){
			if($structure[$col]->hasAttribute('AUTO_INCREMENT')){
				//Store the auto increment field in ORM format
				$this->autoIncrementField = $this->mappings[$col];
				
				//There can only be one AUTO_INCREMENT field per table (also it must be in the PKey)
				break;
			}
		}
		
		//build relation array
		if($this->engine == 'innodb'){
			foreach($structure->relations as $r){
				$this->relations[$r->getField()] = $r->getReference();
			}
		}elseif($this->engine == 'myisam'){
			$this->relations = MyIsam::fieldReferences($structure);
		}else{
			throw new \Exception('Unknown database engine type: '.$this->engine);
		}
		
		//Work out reverse references
		$tableName = $this->tableInfo['name'];
		foreach(TableReference::getAll() as $ref){
			$rStruct = CreateTable::fromTable($ref->getTable());
			foreach($rStruct->relations as $relation){
				$reference = $relation->getReference();
				$rTable = $reference->getTable();
				
				if($rTable == $tableName){
					$this->references[] = array('from_table'=>$ref,'from_field'=>$relation->getField(),'to_table'=>$reference->getTableClass(),'to_field'=>$reference->getColumn());
				}
			}
		}

		//Dnamic Typing data
		$this->dynamicTyping = new DynamicTyping\Instance($table);
		$this->dynamicTyping = $this->dynamicTyping->map;

		//Validation
		$this->validation = new Validation($structure,$this->dynamicTyping);
		
		parent::__construct();
		
		//Store into cache
		Cache::Set($table,$this);
	}
	
	function getMappings($structure = null){
		//Accept a null strucure for when this method is called externally
		if($structure === null){
			$structure = CreateTable::fromTable($this->table);
		}
		
		//Return a Mapping Manager
		return new Mappings($this,$structure);
	}
	
	/**
	 * Make an instance of the parent class. Usually this
	 * is used for storage.
	 * 
	 * @return \Model\Database\ORM\ModelData
	 */
	function toModelData(){
		$r = new ModelData();
		foreach($this as $k=>$v)
			$r->$k = $v;
		
		return $r;
	}
}