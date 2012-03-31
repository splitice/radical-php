<?php
namespace Database\SQL;
class QueryBuilder {
	private $fields = array();
	private $from = '';
	private $joins = array('LEFT'=>array(),'RIGHT'=>array(),'INNER'=>array());
	private $where = array();
	private $order_by = array();
	private $limit = array('start'=>null,'limit'=>null);
	
	function Build(){
		//SELECT
		$sql = 'SELECT ';
		$field = array();
		foreach($this->from as $alias=>$field){
			if(is_numeric($alias)){
				$from[] = $field;
			}else{
				$from[] = $field.' AS '.$alias;
			}
		}
		$sql .= implode(',',$from).' ';
		
		//FROM
		$sql .= 'FROM '.$this->from.' ';
		
		//JOINS
		
		//WHERE
		if($this->where){
			$sql .= 'WHERE '.implode(' AND ',$this->where).' ';
		}
		
		//ORDER BY
		
		//LIMIT
	}
}
?>