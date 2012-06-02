<?php
namespace Database\DBAL;

class MultiQuery {
	const LIMIT = 25000;
	const DELIMETER = ';';
	
	private $con;
	private $sql = array();
	
	function __construct($con){
		$this->con = $con;
	}
	
	function Add($sql){
		$this->sql[] = $sql;
		if(count($this->sql) > self::LIMIT){
			$this->Execute();
		}
		return $this;
	}
	
	function BuildQuery(){
		$sql = '';
		foreach($this->sql as $s){
			$s = rtrim(trim($s),static::DELIMETER);
			$sql .= $s.static::DELIMETER;
		}
		return $sql;
	}
	
	function Execute(){
		$sql = $this->BuildQuery();
		
		if(!$this->con->multi_query($sql)){
			\DB::reConnect();
			$this->con->multi_query($sql);
		}
		
		//Reset SQL storage
		$this->sql = array();
		
		while ($this->con->next_result()) {}
		return $this;
	}
}