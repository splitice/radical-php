<?php
namespace Database\SQL;

class CreateTable extends Internal\StatementBase {
	protected $table;
	protected $fields;
	protected $indexes = array();
	protected $ifNotExists = true;
	protected $engine = 'innodb';
	protected $select;
	
	function __construct($table,$fields = array(),$if_not_exists = true){
		$this->table = $table;
		$this->fields = $fields;
		$this->ifNotExists = $if_not_exists;
	}
	
	function fields(){
		return $this->fields;
	}
	function addField($name,$type){
		$this->fields[$name] = $type;
	}
	function addIndex($name,array $idx){
		if($name == 'FULLTEXT'){
			$this->engine = 'myisam';
		}
		$this->indexes[$name] = $idx;
	}
	function select(SelectStatement $select){
		$this->select = $select;
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
				if($name == 'PRIMARY'){
					$sql .= 'PRIMARY KEY('.implode(',',$field).'),';
				}elseif($name == 'FULLTEXT'){
					$sql .= 'FULLTEXT('.implode(',',$field).'),';
				}else{
					$sql .= 'INDEX('.implode(',',$field).'),';
				}
			}
			$sql = substr($sql,0,-1);
		}else{
			$sql = substr($sql,0,-1);
		}
		
		//End
		$sql .= ')';
		
		//Table Options
		if($this->engine){
			$sql .= ' ENGINE='.$this->engine;
		}
		
		//Select
		if($this->select){
			$sql .= ' '.$this->select;
		}
		
		return $sql;
	}
}