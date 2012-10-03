<?php
namespace Model\Database\DBAL\Prepared;

use Model\Database\DBAL\Adapter\PreparedStatement;

abstract class Common {
	private $statement;
	private $p;
	
	function __construct(\mysqli_stmt $statement,PreparedStatement $p){
		$this->statement = $statement;
		$this->p = $p;
	}
	
	function bind(){
		return call_user_func_array(array($this->statement,'bind_param'),func_get_args());
	}
	
	function next_result(){
		return $this->statement->next_result();
	}
}