<?php
namespace Model\Database\SQL\Parts;
/*
[FROM table_references
    [WHERE where_condition]
    [GROUP BY {col_name | expr | position}
      [ASC | DESC], ... [WITH ROLLUP]]
    [HAVING where_condition]
    [ORDER BY {col_name | expr | position}
      [ASC | DESC], ...]
    [LIMIT {[offset,] row_count | row_count OFFSET offset}]
 */

use Model\Database\SQL\Parts\Expression\TableExpression;
use Model\Database\SQL\Parts\Alias\TableAlias;
use Basic\String\Number;
use Model\Database\SQL\Parse\CreateTable;
use Model\Database\IToSQL;
use Basic\Arr;

class From extends Internal\MergePartBase {
	protected $tables = array();
	protected $joins = array();
	
	/**
	 * @var \Model\Database\SQL\Parts\Where
	 */
	protected $where;
	
	/**
	 * @var \Model\Database\SQL\Parts\GroupBy
	 */
	protected $group_by;
	
	/**
	 * @var \Model\Database\SQL\Parts\Having
	 */
	protected $having;
	
	/**
	 * @var \Model\Database\SQL\Parts\OrderBy
	 */
	protected $order_by;
	
	/**
	 * @var \Model\Database\SQL\Parts\Limit
	 */
	protected $limit;
	
	function __construct(array $tables = null){
		if($tables !== null){
			foreach($tables as $table){
				$this->table($table);
			}
		}
	}
	
	protected function getTableAlias($table){
		foreach($this->tables as $ta=>$ttable){
			if($table == $ttable){
				if(Number::is($ta)){
					return $table;
				}
				return $ta;
			}
		}
	}
	
	function table($table = null,$tablePrefix = null){
		if($table === null){
			return $this->table;
		}else{
			if($tablePrefix === null){
				if(is_array($table)){
					if(Arr::is_assoc($table)){
						foreach($table as $k=>$t){
							$this->table($t,$k);
						}
					}else{
						foreach($table as $t){
							$this->table($t);
						}
					}
					return;
				}
				$tablePrefix = $table;
			}elseif($tablePrefix){
				$table = new Alias\TableAlias($table,$tablePrefix);
			}
			
			if(isset($this->table[$tablePrefix])){
				throw new \Exception('Table Alias "'.$tablePrefix.'" already exists in this query.');
			}
			
			$this->tables[$tablePrefix] = $table;
		}
		return $this;
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
						$where[$k] = new TableExpression($v[1],$v[0]);
					}
				}
	
				$where = implode(' ',$where);
			}
		}
	
		$class = '\\Model\\Database\\SQL\\Parts\\Join\\'.ucfirst($type).'Join';
		$this->joins[$alias] = new $class($table.' '.$alias,$where);
		return $this;
	}
	
	function joins(){
		return $this->joins;
	}
	
	function where($where = null){
		if($where === null){
			if($this->where === null) $this->where = new Where();
			return $this->where;
		}else{
			if(is_string($where)){
				$where = array($where);
			}
			if(is_array($where) || !($where instanceof Where)){
				$this->where = new Where($where);
			}else
				$this->where = $where;
		}
		return $this;
	}
	function where_and($and){
		$where = $this->where();
		$where[] = new WhereAND($and);
		return $this;
	}
	function where_or($or){
		$where = $this->where();
		$where[] = new WhereOR($or);
		return $this;
	}
	
	function group($group = null){
		if($group === null) return $this->group_by;
		
		if($group instanceof GroupBy){
			$this->group_by = $group;
		}else{
			$this->group_by = new GroupBy($group);
		}
		
		return $this;
	}
	
	/**
	 * @param mixed $group
	 * @return \Model\Database\SQL\Parts\From
	 */
	function group_by($group){
		return $this->group($group);
	}
	
	/**
	 * OrderBy
	 * expr,ASC
	 * array(expr,ASC)
	 * 
	 * @param mixed $order_by
	 * @throws \Exception
	 * @return \Model\Database\SQL\Parts\OrderBy|\Database\SQL\Parts\From
	 */
	function order_by($order_by = null,$order = null){
		if($order_by === null) return $this->order_by;
		if($order !== null && is_string($order_by)){
			$order_by = array(array($order_by,$order));
		}
		$this->order_by = new OrderBy($order_by);
		
		return $this;
	}
	
	/**
	 * \Model\Database\SQL\Parts\Limit,null = $limit
	 * int,int = $start,$end
	 * null,int = $start,$end
	 * int,null = $end
	 * null,null = return limit
	 * array(a1,a2) = limit(a1,a2)
	 * 
	 * @param mixed $start
	 * @param int $end
	 * @throws \Exception
	 * @return \Model\Database\SQL\Parts\Limit|\Database\SQL\Parts\From
	 */
	function limit($start = null,$end = null){
		if($start == null && $end == null){
			return $this->limit;
		}elseif($start instanceof Limit){
			$this->limit = $start;
		}elseif(is_array($start)){
			if(count($start) != 2) throw new \Exception('Invalid Limit format array cant have '.count($start).' members');
			
			$this->limit = new Limit($start[0],$start[1]);
		}elseif($end===null){
			$this->limit = new Limit(null,$start);
		}else{
			$this->limit = new Limit($start,$end);
		}
		return $this;
	}
	
	function remove_limit(){
		$this->limit = null;
	}
	function remove_joins(){
		$this->joins = array();
	}
	function remove_order_by(){
		$this->order_by = null;
	}
	
	function toSQL(){
		//FROM
		$ret = 'FROM ';
		if(is_array($this->tables)){
			$ret .= implode(', ',array_map(function($e){
				return '`'.$e.'`';
			},$this->tables));
		}
		
		//Joins
		if($this->joins){
			$ret .= ' '.implode(' ',$this->joins);
		}
		
		//WHERE
		if($this->where){
			$ret .= ' '.$this->where;
		}
		
		//GROUP BY
		if($this->group_by){
			$ret .= ' '.$this->group_by;
		}
		
		//HAVING
		if($this->having){
			$ret .= ' '.$this->having;
		}
		
		//ORDER BY
		if($this->order_by){
			$ret .= ' '.$this->order_by;
		}
		
		//LIMIT
		if($this->limit){
			$ret .= ' '.$this->limit;
		}
		
		return $ret;
	}
}