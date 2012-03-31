<?php
namespace Database\SQL;

use Database\Model\TableReferenceInstance;

class ShowCreateTable extends Internal\StatementBase {
	protected $table;

	function __construct($table){
		if($table instanceof TableReferenceInstance){
			$table = constant($table->getClass().'::TABLE');
		}
		$this->table = $table;
	}
	
	function toSQL(){
		return 'SHOW CREATE TABLE `'.$this->table.'`';
	}
}