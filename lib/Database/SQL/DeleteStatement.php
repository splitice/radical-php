<?php
namespace Database\SQL;

use Database\DBAL\Adapter\Instance;

class DeleteStatement extends Internal\StatementBase {
	protected $table;
	protected $where;
	
	function __construct($table,$where){
		$this->table = $table;
		$this->where = $where;
	}
	
	private function _whereBuild(Instance $db){		
		$w = new Parts\Where($this->where);
		return $w->toSQL(true);
	}
	
	function toSQL(){
		$db = \DB::getInstance();
		
		//Build Query
		$sql = 'DELETE FROM `' . $this->table . '` ';
		$sql .= $this->_whereBuild($db);

		return $sql;
	}
}