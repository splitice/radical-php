<?php
namespace Model\Database\SQL;

use Model\Database\Model\TableReferenceInstance;

class LockTable extends Internal\StatementBase {
	protected $table;
	protected $mode;

	function __construct($table,$mode){
		if($table instanceof TableReferenceInstance){
			$table = $table->getTable();
		}
		$this->table = $table;
		$this->mode = strtoupper($mode);
		if($this->mode != 'READ' && $this->mode != 'WRITE')
			throw new \Exception('Invalid Lock mode');
	}
	
	function toSQL(){
		return 'LOCK TABLES `'.$this->table.'` '.$this->mode;
	}
}