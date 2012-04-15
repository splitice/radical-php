<?php
namespace Database\SQL;

use Database\DBAL;

class SelectStatement extends Internal\StatementBase {
	protected $table;
	protected $fields;
	protected $where = array();
	protected $order_by;
	protected $limit;
	protected $group;
	
	function __construct($table = null, $fields = '*'){
		$this->table = $table;
		$this->fields = $fields;
	}
	
	function fields($fields = null){
		if($fields === null){
			return $this->fields;
		}else{
			$this->fields = $fields;
			$this->sql = null;
		}
		return $this;
	}
	
	function where($where = null){
		if($where === null){
			return $this->where;
		}else{
			if(is_string($where)){
				$where = array($where);
			}
			if(is_array($where)){
				$where = new \Database\SQL\Parts\Where($where);
			}
			$this->where = $where;
			$this->sql = null;
		}
		return $this;
	}
	function where_and($where){
		$this->where[] = $where;
		$this->sql = null;
		return $this;
	}
	
	function group($group){
		$this->group = $group;
		$this->sql = null;
		return $this;
	}
	function group_by($group){
		return $this->group($group);
	}
	function orderBy($order_by){
		$this->order_by = $order_by;
		$this->sql = null;
		return $this;
	}
	
	function limit($start,$end){
		if($start == null && $end == null){
			$this->limit = null;
		}elseif($start == null){
			$this->limit = $end;
		}elseif($end == null){
			$this->limit = $start;
		}else{
			$this->limit = array($start,$end);
		}
		$this->sql = null;
		return $this;
	}
	
	function from($table = null){
		if($table === null){
			return $this->table;
		}else{
			$this->table = $table;
			$this->sql = null;
		}
		return $this;
	}
	
	function _enc1($a){
		$sql = '';
		if(is_array($a)){
			$first = true;
			foreach($a as $k=>$v){
				if(!$first){
					$sql .= ', ';
				}
				$first = false;
				$sql .= '`'.$v.'`';
				if(!is_numeric($k)){
					$sql .= ' AS `'.$k.'`';
				}
			}
		}else{
			$sql .= $a;
		}
		return $sql;
	}
	
	private $sql;
	function toSQL(){
		if($this->sql){
			return $this->sql;
		}
		
		//Build Query
		$sql = 'SELECT '.$this->_enc1($this->fields);

		$sql .= ' FROM '.$this->_enc1($this->table);
		if($this->where){
			if(is_array($this->where)){
				$sql .= ' WHERE '.implode(' AND ',$this->where);
			}else{
				$where = (string)$this->where;
				if($where)
					$sql .= ' WHERE '.$where;
			}
		}
		
		if($this->group){
			$group = $this->group;
			if(is_array($group)){
				$group = implode(',',$group);
			}
			$sql .= ' GROUP BY '.$group;
		}
		
		if($this->order_by){
			$order = $this->order_by;
			if(is_array($order)){
				$order = implode(',',$order);
			}
			$sql .= ' ORDER BY '.$order;
		}
		
		if($this->limit){
			$limit = $this->limit;
			if(is_array($limit)){
				$limit = implode(',',$limit);
			}
			$sql .= ' LIMIT '.$limit;
		}
		
		$this->sql = $sql;
		return $sql;
	}
	function getCount(){
		//Check for entry
		$count = clone $this;
		$count->fields('COUNT(*)');
	
		$res = \DB::Query($count);
		return $res->Fetch(DBAL\Fetch::FIRST,new \Cast\Integer());
	}
}