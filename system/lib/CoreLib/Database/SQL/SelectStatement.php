<?php
namespace Database\SQL;

class SelectStatement extends Internal\StatementBase {
	protected $table;
	protected $fields;
	protected $where = '1';
	protected $order_by;
	protected $limit;
	
	function __construct($table = null, $fields = '*'){
		$this->table = $table;
		$this->fields = $fields;
	}
	
	function fields($fields){
		$this->fields = $fields;
		$this->sql = null;
		return $this;
	}
	
	function where($where){
		if(is_array($where)){
			$where = new \Database\SQL\Parts\Where($where);
		}
		$this->where = $where;
		$this->sql = null;
		return $this;
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
	
	function from($table){
		$this->table = $table;
		$this->sql = null;
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
			$sql .= ' WHERE '.$this->where;
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
}