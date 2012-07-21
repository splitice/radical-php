<?php
namespace Model\Database\SQL;

use Model\Database\DBAL\Adapter\Instance;

class UpdateStatement extends Internal\StatementBase {
	protected $table;
	protected $set;
	protected $where;
	
	function __construct($table = null,$values = array(),$where = array()){
		$this->table = $table;
		$this->set = new Parts\Set($values);
		$this->where = new Parts\Where($where);
	}
	
	function set($set = null,$second = null){
		if($set === null){
			if($this->set === null) $this->set = new Parts\Set();
			return $this->set;
		}else{
			if($second !== null){
				if(!is_scalar($set)){
					throw new \Exception('Invalid Set, not scalar with two parameters');
				}
				$set = array($set=>$second);
			}
			if(is_string($set)){
				$set = array($set);
			}
			if(is_array($set)){
				$set = new Parts\Set($set);
			}
			$this->set = $set;
		}
		return $this;
	}
	
	function where($where = null,$second = null){
		if($where === null){
			if($this->where === null) $this->where = new Parts\Where();
			return $this->where;
		}else{
			if($second !== null){
				if(!is_scalar($where)){
					throw new \Exception('Invalid Where, not scalar with two parameters');
				}
				$where = array($where=>$second);
			}
			if(is_string($where)){
				$where = array($where);
			}
			if(is_array($where)){
				$where = new Parts\Where($where);
			}
			$this->where = $where;
		}
		return $this;
	}
	function where_and($and){
		$where = $this->where();
		$where[] = new Parts\WhereAND($and);
		return $this;
	}
	
	private function _setBuild(Instance $db){
		$ret = array();
		foreach($this->values as $k=>$v){
			$ret[] = '`'.$k.'`='.$db->Escape($v);
		}
		return implode(',',$ret);
	}
	
	function toSQL(){
		$db = \DB::getInstance();
		
		//Build Query
		$sql = 'UPDATE `' . $this->table . '` ';
		$sql .= $this->set.' ';
		$sql .= $this->where;

		return $sql;
	}
}