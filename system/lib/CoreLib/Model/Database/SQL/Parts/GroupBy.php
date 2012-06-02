<?php
namespace Model\Database\SQL\Parts;

use Model\Database\SQL\Parts\Expression\TableExpression;

use Basic\Arr;

class GroupBy extends Internal\ArrayPartBase {
	function _Set($k,$v){
		if($k === null || \Basic\String\Number::is($k)){
			if(is_array($v)){
				if(Arr::is_assoc($v)){
					foreach($v as $k=>$vv){
						$this->_Add($k,$vv);
					}
				}else{
					foreach($v as $k=>$vv){
						$this->_Add(null,$vv);
					}
				}
			}elseif($v !== null){
				//Add(expr)
				$this->data[] = $v;
			}
		}else{
			//Add(table,field) -> TableExpression
			$this->data[] = new TableExpression($v,$k);
		}
	}
	function toSQL(){
		$ret = implode(', ',$this->data);
		if($ret){
			$ret = 'GROUP BY '.$ret;
		}
		return $ret;
	}
}