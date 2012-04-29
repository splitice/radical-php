<?php
namespace Database\SQL\Parts;

class OrderBy extends Internal\ArrayPartBase {
	function Add($v){
		//Add(expr,order)
		
		//Add(OrderByPart)
		
		//Add(array(expr1,expr2))
		
		//Add(array(array(expr1,order1 = ASC),array(expr1,order2,order1 = ASC)))
	}
	function toSQL(){
		$ret = implode(', ',$this->data);
		if($ret){
			$ret = 'ORDER BY '.$ret;
		}
		return $ret;
	}
}