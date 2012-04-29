<?php
namespace Database\SQL\Parts\Internal;

use Database\SQL\Parts\Expression\Comparison;

use Database\IToSQL;

use Database\SQL\Parts\WhereAND;

use Basic\Arr;

abstract class FilterPartBase extends ArrayPartBase {
	const PART_NAME = '*INVALID*';
	
	function _Set($k,$v){
		if($k === null){			
			if($v instanceof IToSQL){
				//Add(WhereAnd | WhereOr) -> Append
				//Add(Statement) -> WhereAnd -> Append
				$this->data[] = $v;
			}elseif(is_string($v)){
				//Add(string) -> Statement -> Add
				$this->data[] = new WhereAND($v);
			}elseif(is_array($v)){
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
					$this->data[] = new Comparison($v[0],$v[1],$op);
				}
			}else{
				throw new \Exception('Unknown format for add');
			}
		}elseif(is_numeric($k) && ((int)$k == (float)$k)){
			throw new \Exception('Cant set based on exact offset');
		}else{
			//Assosiative simple syntax
			$this->data[] = WhereAND::fromAssign($k,$v);
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