<?php
namespace Model\Database\SQL\Parts\Internal;

use Basic\String\Number;

use Model\Database\SQL\Parts\Expression\IComparison;

use Model\Database\SQL\Parts\Expression\Comparison;

use Model\Database\IToSQL;

use Model\Database\SQL\Parts\WhereAND;

use Basic\Arr;

abstract class FilterPartBase extends ArrayPartBase {
	const PART_NAME = '*INVALID*';
	const AUTO_NULL = true;
	
	function _Set($k,$v){
		if($k === null || Number::is($k) ){			
			if($v instanceof IToSQL){
				//Add(WhereAnd | WhereOr) -> Append
				//Add(Statement) -> WhereAnd -> Append
				$this->data[] = $v;
			}elseif(is_string($v)){
				//Add(string) -> Statement -> Add
				$this->data[] = new WhereAND($v);
			}elseif(is_array($v)){
				if(!$v){
					return;//Empty array
				}
				if(Arr::is_assoc($v)){
					//Add(array(array('field'=>'value'))) -> Statement -> Add
					foreach($v as $k=>$vv){
						$this->_Set($k, $vv);
					}
				}elseif(isset($v[0]) && is_array($v[0])){
					foreach($v as $vv){
						$this->_Set(null,$vv);
					}
				}else{
					//Add(array(expr1,comparison,expr2)) -> Statement -> Add
					$op = null;
					if(count($v) == 2){
						$op = '=';
					}
					if(count($v) == 3){
						$op = $v[1];
						$v = array($v[0],$v[1]);
					}
					if($op === null){
						throw new \Exception('Invalid array format');
					}
					$this->data[] = new Comparison($v[0],$v[1],$op,static::AUTO_NULL);
				}
			}else{
				throw new \Exception('Unknown format for add');
			}
		}else{
			//Assosiative simple syntax
			$op = '=';
			if($v instanceof IComparison){
				$op = '';
			}
			$this->data[] = WhereAND::fromAssign($k,$v,$op,static::AUTO_NULL);
		}
	}
	function toSQL(){
		$ret = '';
		foreach(array_values($this->data) as $k=>$v){
			$ret .= $v->toSQL(!$k);
		}
		if($ret){
			$ret = static::PART_NAME.' '.$ret;
		}
		return $ret;
	}
}