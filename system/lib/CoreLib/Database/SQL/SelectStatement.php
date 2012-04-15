<?php
namespace Database\SQL;

use Database\SQL\Parts\Where;

use Basic\Arr;

use Database\SQL\Parse\CreateTable;

use Database\IToSQL;

use Database\DBAL;

class SelectStatement extends Internal\StatementBase {
	protected $table = array();
	protected $fields;
	protected $where = array();
	protected $order_by;
	protected $limit;
	protected $group;
	protected $join = array('left'=>array(),'inner'=>array(),'right'=>array());
	
	function __construct($table = null, $fields = '*'){
		$this->fields($fields);
		$this->from($table);
	}
	
	function getTableAlias($table){
		foreach($this->table as $prefix=>$t){
			if($t == $table){
				return $prefix;
			}
		}
	}
	function left_join($table, $alias, $on = null){
		return $this->join($table, $alias, $on, 'left');
	}
	function right_join($table, $alias, $on = null){
		return $this->join($table, $alias, $on, 'right');
	}
	function inner_join($table, $alias, $on = null){
		return $this->join($table, $alias, $on, 'inner');
	}
	function join($table, $alias, $on = null, $type = 'left'){
		$this->sql = null;
		$where = $on;
		if(!is_string($where) && !($where instanceof IToSQL)){
			if($where === null){
				//Automatically detirmine linkage using foreign keys
				$ct = CreateTable::fromTable($table);
				foreach($ct->relations as $rk=>$relation){
					$reference = $relation->getReference();
					
					//TODO: Check against other joins
					if($rightAlias = $this->getTableAlias($reference->getTable())){
						$where = array(array($alias,$relation->getField()),array($rightAlias,$reference->getColumn()));
					}
				}
			}
			if(is_array($where)){
				//array('leftSide','rightSide');
				//or array('leftSide','=','rightSide')
				//WHERE
				//leftSide = array('alias','field')
				//or 'leftSide'
				
				if(count($where) == 2){
					$where = array($where[0],'=',$where[1]);
				}
				
				//Resolve out arrayed members
				foreach($where as $k=>$v){
					if(is_array($v)){
						$where[$k] = $this->_encFieldRef($v[0],$v[1]);
					}
				}
				
				$where = implode(' ',$where);
			}
		}
		
		$this->join[$type][$alias] = compact('table','where');
		return $this;
	}
	
	function joins(){
		return $this->join;
	}
	
	function fields($fields = null){
		if($fields === null){
			return $this->fields;
		}else{
			if(is_string($fields)){
				$fields = array($fields);
			}
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
		if($this->where instanceof Where){
			if((string)$this->where){
				$this->where = array($this->where);
			}else{
				$this->where = array();
			}
		}
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
	
	function from($table = null,$tablePrefix = null){
		if($table === null){
			return $this->table;
		}else{
			if($tablePrefix === null){
				if(is_array($table)){
					if(Arr::is_assoc($table)){
						foreach($table as $k=>$t){
							$this->from($t,$k);
						}
					}else{
						foreach($table as $t){
							$this->from($t);
						}
					}
					return;
				}
				$tablePrefix = $table;
			}
			
			$this->table[$tablePrefix] = $table;
			$this->sql = null;
		}
		return $this;
	}
	
	private function _enc1($a){
		$sql = '';
		foreach($a as $k=>$v){
			if($sql){
				$sql .= ', ';
			}
			$sql .= '`'.$v.'`';
			if(!is_numeric($k) && $k != $v){
				$sql .= ' AS `'.$k.'`';
			}
		}
		return $sql;
	}
	private function _encFieldRef($alias,$field){
		$sql = '';
		if($alias){
			$sql .= '`'.$alias.'`.';
		}
		$sql .= '`'.$field.'`';
		return $sql;
	}
	private function _encFields($a){
		$sql = '';
		foreach($a as $k=>$v){
			if($sql){
				$sql .= ', ';
			}
			if(is_array($v)){
				$sql .= $this->_encFieldRef($v[0],$v[1]);
			}else{
				$sql .= $v;
			}
			if(!is_numeric($k) && $k != $v){
				$sql .= ' AS `'.$k.'`';
			}
		}
		return $sql;
	}
	
	private $sql;
	function toSQL(){
		if($this->sql){
			return $this->sql;
		}
		
		//Build Query
		$sql = 'SELECT '.$this->_encFields($this->fields);

		//FROM
		$sql .= ' FROM '.$this->_enc1($this->table);
		
		//JOIN
		if($this->join){
			foreach($this->join as $joinType=>$joins){
				foreach($joins as $joinAlias => $join){
					$sql .= ' '.strtoupper($joinType).' JOIN '.$join['table'].' AS '.$joinAlias;
					$sql .= ' ON ('.$join['where'].')';
				}
			}
		}
		
		//WHERE
		if($this->where){
			if(is_array($this->where)){
				$sql .= ' WHERE ';
				foreach(array_values($this->where) as $k=>$w){
					if($k){
						$sql .= ' AND ';
					}
					$sql .= $w;
				}
			}else{
				$where = (string)$this->where;
				if($where)
					$sql .= ' WHERE '.$where;
			}
		}
		
		//GROUP BY
		if($this->group){
			$group = $this->group;
			if(is_array($group)){
				$group = implode(',',$group);
			}
			$sql .= ' GROUP BY '.$group;
		}
		
		//ORDER BY
		if($this->order_by){
			$order = $this->order_by;
			if(is_array($order)){
				$order = implode(',',$order);
			}
			$sql .= ' ORDER BY '.$order;
		}
		
		//LIMIT
		if($this->limit){
			$limit = $this->limit;
			if(is_array($limit)){
				$limit = implode(',',$limit);
			}
			$sql .= ' LIMIT '.$limit;
		}
		
		//Cache and return
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