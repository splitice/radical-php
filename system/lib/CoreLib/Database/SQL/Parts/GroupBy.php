<?php
namespace Database\SQL\Parts;

class GroupBy extends Internal\ArrayPartBase {
	function __construct($expr){
		//Add(array(array(table,field),...))
		foreach($expr as $e){
			
		}
		
		//Add(expr) [AND]
		//Add(array(table,field))
		$this->Add($expr);
		
		//Add(table,field)
		$this->Add(func_get_args());
	}
	function Add($v){
		//Add(expr)
		
		//Add(array(table,field)) -> TableExpression
		
		//Add(table,field) -> TableExpression
	}
	function toSQL(){
		$ret = implode(', ',$this->data);
		if($ret){
			$ret = 'GROUP BY '.$ret;
		}
		return $ret;
	}
}