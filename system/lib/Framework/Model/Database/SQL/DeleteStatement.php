<?php
namespace Model\Database\SQL;

use Model\Database\DBAL\Instance;
use Model\Database\SQL\Parts\From;

class DeleteStatement extends Internal\StatementBase {	
	protected $from;
	
	function __construct($table, $where){
		if($table !== null && !is_array($table)){
			$table = array($table);
		}
		$this->from = new From($table);
		$this->where($where);
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
		return $this->_R($this->from->where_or($where));
	}
	
	function group($group = null){
		return $this->_R($this->from->group(func_get_args()));
	}
	function group_by($group){
		return $this->group($group);
	}
	function order_by($order_by,$order = null){
		return $this->_R($this->from->order_by($order_by,$order));
	}
	
	function limit($start = null,$end = null){
		return $this->_R($this->from->limit($start,$end));
	}
	
	function remove_limit(){
		return $this->_R($this->from->remove_limit());
	}
	function remove_joins(){
		return $this->_R($this->from->remove_joins());
	}
	function remove_order_by(){
		return $this->_R($this->from->remove_order_by());
	}
	
	function __clone(){
		$this->from = clone $this->from;
	}
	
	function toSQL(){
		$ret = 'DELETE '.$this->from;
		return $ret;
	}
}