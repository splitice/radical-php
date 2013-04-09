<?php
namespace Model\Database\Model;

class InsertBuffer {
	private $table;
	private $data = array();
	
	function __construct(TableReferenceInstance $table){
		$this->table = $table;
	}
	
	function add(Table $table){
		$this->data[] = $table->toSQL();
	}
	
	function insert(){
		if($this->data){
			$ret = $this->table->insert($this->data)->query();
			$this->data = array();
			return $ret;
		}
	}
}