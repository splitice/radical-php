<?php
namespace Database\ORM;

use Database\SQL\Parse\CreateTable\ColumnReference;

use Database\Model\TableReference;

use Database\Model\TableReferenceInstance;

use Database\SQL\Parse\CreateTable;

class Model extends ModelData {	
	private function fieldReferences(CreateTable $structure){
		$ret = array();
		foreach($structure as $field=>$statement){
			$ref = ModelReference::Find($field);
			if($ref != $this->table){
				$ret[$field] = new ColumnReference($ref->getTable(), $field);
			}
		}
		return $ret;
	}
	function __construct(TableReferenceInstance $table){
		$this->table = $table;
		$this->tableInfo = $table->Info();
		$structure = CreateTable::fromTable($table);
		$this->engine = $structure->engine;
		
		if(isset($structure->indexes['PRIMARY'])){
			$this->id = $structure->indexes['PRIMARY']->getKeys();
		}
		
		//Build mapping translation array=
		$this->mappings = $this->getMappings($structure)->translationArray();
		
		//build relation array
		if($this->engine == 'innodb'){
			foreach($structure->relations as $r){
				$this->relations[$r->getField()] = $r->getReference();
			}
		}elseif($this->engine == 'myisam'){
			$this->relations = $this->fieldReferences($structure);
		}else{
			throw new \Exception('Unknown database engine type: '.$this->engine);
		}
		
		//Work out reverse references
		$tableName = $this->tableInfo['name'];
		foreach(TableReference::getAll() as $ref){
			$rStruct = CreateTable::fromTable($ref);
			foreach($rStruct->relations as $relation){
				$reference = $relation->getReference();
				$rTable = $reference->getTable();
				
				if($rTable == $tableName){
					$this->depends[$reference->getTableClass()] = array('table'=>$ref,'from'=>$relation->getField(),'to'=>$reference->getColumn());
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
		if($structure === null){
			$structure = CreateTable::fromTable($this->table);
		}
		return new Mappings($this,$structure);
	}
	
	function toModelData(){
		$r = new ModelData();
		foreach($this as $k=>$v){
			$r->$k = $v;
		}
		return $r;
	}
}