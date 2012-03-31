<?php
namespace Database\SQL;

class CreateTable extends Internal\StatementBase {
	protected $table;
	protected $fields;
	protected $indexes = array();
	protected $ifNotExists = true;
	
	function __construct($table,$fields = array(),$if_not_exists = true){
		$this->table = $table;
		$this->fields = $fields;
		$this->ifNotExists = $if_not_exists;
	}
	
	function addFeild($name,$type){
		$this->fields[$name] = $type;
	}
	function addIndex($name,array $idx){
		$this->indexes[$name] = $idx;
	}
	
	function toSQL(){
		//$db = \DB::getInstance();
		
		//Start
		$sql = 'CREATE TABLE ';
		if($this->ifNotExists){
			$sql .= 'IF NOT EXISTS ';
		}
		$sql .= $this->table;
		$sql .= ' (';
		
		//Fields
		foreach($this->fields as $name=>$type){
			$sql .= $name.' '.$type.',';
		}
		
		//Indexes
		if($this->indexes){
			foreach($this->indexes as $name=>$field){
				if(strtoupper($name) == 'PRIMARY'){
					$sql .= 'PRIMARY KEY('.implode(',',$field).'),';
				}
			}
			$sql = substr($sql,0,-1);
		}else{
			$sql = substr($sql,0,-1);
		}
		
		//End
		$sql .= ')';
		
		return $sql;
	}
}