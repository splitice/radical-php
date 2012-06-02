<?php
namespace Database\SQL\Parse\CreateTable;

use Database\Model\TableReferenceInstance;
use Database\Model\TableReference;

class ColumnReference{
	protected $table;
	protected $column;
	
	function __construct($table,$column){
		$this->table = $table;
		$this->column = $column;
	}
	
	function getTableClass(){
		$i = $this->getTableReference();
		return $i->getClass();
	}
	
	function getTableReference(){
		return TableReference::getByTableName($this->table);
	}
	
	/**
	 * @return the $table
	 */
	public function getTable() {
		return $this->table;
	}

	/**
	 * @return the $column
	 */
	public function getColumn() {
		return $this->column;
	}
}