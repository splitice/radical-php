<?php
namespace Database\SQL;

use Database\DBAL\Adapter\Instance;

class UpdateStatement extends Internal\StatementBase {
	protected $table;
	protected $values;
	protected $where;
	
	function __construct($table = null,$values = array(),$where = array()){
		$this->table = $table;
		$this->values = $values;
		$this->where = $where;
	}
	
	private function _setBuild(Instance $db){
		$ret = array();
		foreach($this->values as $k=>$v){
			$ret[] = '`'.$k.'`='.$db->Escape($v);
		}
		return implode(',',$ret);
	}
	
	private function _whereBuild(Instance $db){		
		$w = new Parts\Where($this->where);
		return $w->toSQL(true);
	}
	
	function toSQL(){
		$db = \DB::getInstance();
		
		//Build Query
		$sql = 'UPDATE `' . $this->table . '` SET ';
		$sql .= $this->_setBuild($db).' ';
		$sql .= $this->_whereBuild($db);

		return $sql;
	}
}