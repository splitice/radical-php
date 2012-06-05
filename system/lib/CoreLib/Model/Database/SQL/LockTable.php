<?php
namespace Model\Database\SQL;

use Model\Database\Model\TableReferenceInstance;

class LockTable extends Internal\StatementBase {
	protected $table;
	protected $mode;

	function __construct($table,$mode = null){
		if($table instanceof TableReferenceInstance){
			$table = $table->getTable();
		}
		$this->table = $table;
		if($mode !== null){
			$this->mode = strtoupper($mode);
			if($this->mode != 'READ' && $this->mode != 'WRITE')
				throw new \Exception('Invalid Lock mode');
		}
	}
	
	function toSQL(){
		$sql = 'LOCK TABLES ';
		if(is_array($this->table)){
			foreach($this->table as $table=>$mode){
				$sql .= '`'.$table.'` '.$mode;
			}
		}else{
			$sql .= '`'.$this->table.'` '.$this->mode;
		}
	}
}