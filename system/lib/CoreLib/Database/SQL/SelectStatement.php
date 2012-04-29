<?php
namespace Database\SQL;

use Database\SQL\Parts\From;

use Database\SQL\Parts\Where;

use Basic\Arr;

use Database\SQL\Parse\CreateTable;

use Database\IToSQL;

use Database\DBAL;

/*
http://dev.mysql.com/doc/refman/5.5/en/select.html

SELECT
    [ALL | DISTINCT | DISTINCTROW ]
      [HIGH_PRIORITY]
      [STRAIGHT_JOIN]
      [SQL_SMALL_RESULT] [SQL_BIG_RESULT] [SQL_BUFFER_RESULT]
      [SQL_CACHE | SQL_NO_CACHE] [SQL_CALC_FOUND_ROWS]
    select_expr [, select_expr ...]
    [FROM table_references
    [WHERE where_condition]
    [GROUP BY {col_name | expr | position}
      [ASC | DESC], ... [WITH ROLLUP]]
    [HAVING where_condition]
    [ORDER BY {col_name | expr | position}
      [ASC | DESC], ...]
    [LIMIT {[offset,] row_count | row_count OFFSET offset}]
    [PROCEDURE procedure_name(argument_list)]
    [INTO OUTFILE 'file_name'
        [CHARACTER SET charset_name]
        export_options
      | INTO DUMPFILE 'file_name'
      | INTO var_name [, var_name]]
    [FOR UPDATE | LOCK IN SHARE MODE]]
 */


class SelectStatement extends Internal\StatementBase {
	protected $fields = array();
	protected $from;
	
	
	function __construct($table = null, $fields = '*'){
		if($table !== null && !is_array($table)){
			$table = array($table);
		}
		$this->from = new From($table);
		
		$this->fields($fields);
	}
	
	function fields($fields = null){
		if($fields === null){
			return $this->fields;
		}else{
			if(is_string($fields)){
				$fields = array($fields);
			}
			$this->fields = $fields;
		}
		return $this;
	}
	
	private function _R($returned){
		//Ensure chaining is to the right object (Encapsulation)
		if($returned === $this->from) return $this;
		return $returned;
	}
	
	function from($table = null,$tablePrefix = null){
		return $this->_R($this->from->table($table,$tablePrefix));
	}
	
	/* Joins */
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
		return $this->_R(call_user_func_array(array($this->from,__FUNCTION__), func_get_args()));
	}
	
	function joins(){
		return $this->_R($this->from->joins());
	}
	
	function where($where = null){
		return $this->_R($this->from->where($where));
	}
	function where_and($where){
		return $this->_R($this->from->where_and($where));
	}
	function where_or($where){
		return $this->_R($this->from->where_and($where));
	}
	
	function group($group = null){
		return $this->_R($this->from->group(func_get_args()));
	}
	function group_by($group){
		return $this->group($group);
	}
	function order_by($order_by = null){
		return $this->_R($this->from->order_by($order_by));
	}
	
	function limit($start = null,$end = null){
		return $this->_R($this->from->limit($start,$end));
	}
	
	function getCount(){
		//Check for entry
		$count = clone $this;
		$count->fields('COUNT(*)');
	
		$res = \DB::Query($count);
		return $res->Fetch(DBAL\Fetch::FIRST,new \Cast\Integer());
	}
	
	function toSQL(){
		$ret = 'SELECT '.implode(', ',$this->fields).' '.$this->from;
		return $ret;
	}
}