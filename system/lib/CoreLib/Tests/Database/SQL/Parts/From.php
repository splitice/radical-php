<?php
namespace Tests\Database\SQL\Parts;

use Debug\Test\IUnitTest;
use Debug\Test\Unit;

class From extends Unit implements IUnitTest {
	protected $tables = array();
	protected $joins = array();
	
	/**
	 * @var \Database\SQL\Parts\Where
	 */
	protected $where;
	
	/**
	 * @var \Database\SQL\Parts\GroupBy
	 */
	protected $group_by;
	
	/**
	 * @var \Database\SQL\Parts\Having
	 */
	protected $having;
	
	/**
	 * @var \Database\SQL\Parts\OrderBy
	 */
	protected $order_by;
	
	/**
	 * @var \Database\SQL\Parts\Limit
	 */
	protected $limit;
	
	function __construct(array $tables = null){
		if($tables !== null){
			foreach($tables as $table){
				$this->table($table);
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
		return $this->joins;
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
		return $this;
	}
	
	function group($group = null){
		if($group === null) return $this->group_by;
		
		if($group instanceof GroupBy){
			$this->group = $group;
		}else{
			$this->group = new GroupBy($group);
		}
		
		return $this;
	}
	
	/**
	 * @param mixed $group
	 * @return \Database\SQL\Parts\From
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
	 * @return \Database\SQL\Parts\OrderBy|\Database\SQL\Parts\From
	 */
	function order_by($order_by = null){
		if($order_by === null) return $this->order_by;
		
		if($order_by instanceof OrderBy){
			$this->order_by = $order_by;
		}elseif(is_array($order_by)){
			if(count($order_by) == 2){
				$this->order_by = new OrderBy($order_by[0],$order_by[1]);
			}else{
				throw new \Exception('Invalid Order By call array');
			}
		}elseif(func_get_arg(1) !== false){
			$args = func_get_args();
			return $this->order_by($args);
		}else{
			throw new \Exception('Invalid order by call');
		}
		
		return $this;
	}
	
	/**
	 * \Database\SQL\Parts\Limit,null = $limit
	 * int,int = $start,$end
	 * null,int = $start,$end
	 * int,null = $end
	 * null,null = return limit
	 * array(a1,a2) = limit(a1,a2)
	 * 
	 * @param mixed $start
	 * @param int $end
	 * @throws \Exception
	 * @return \Database\SQL\Parts\Limit|\Database\SQL\Parts\From
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
	
	function toSQL(){
		//FROM
		$ret = 'FROM ';
		if(is_array($this->tables)){
			$ret .= implode(', ',$this->tables);
		}
		if(!implode(', ',$this->tables)){
			debug_print_backtrace();
			die(var_dump($this->tables));
		}
		
		//Joins
		if($this->joins){
			$ret .= implode(' ',$this->joins);
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