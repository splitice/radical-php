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
		foreach(\ClassLoader::getNSExpression(\ClassLoader::getProjectSpace('DB\\*')) as $class){
			if($class::TABLE == $this->table){
				return $class;
			}
		}
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