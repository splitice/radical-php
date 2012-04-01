<?php
namespace Database\ORM;

use Database\Model\TableReference;

use Database\Model\TableReferenceInstance;

use Database\SQL\Parse\CreateTable;

class Model extends ModelData {	
	function __construct(TableReferenceInstance $table){
		$this->table = $table;
		$this->tableInfo = $table->Info();
		$structure = CreateTable::fromTable($table);

		if(isset($structure->indexes['PRIMARY'])){
			$this->id = $structure->indexes['PRIMARY']->getKeys();
		}
		
		//Build mapping translation array=
		$this->mappings = $this->getMappings($structure)->translationArray();
		
		//build relation array
		foreach($structure->relations as $r){
			$this->relations[$r->getField()] = $r->getReference();
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
		
		$this->validation = new Validation($structure);
		
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