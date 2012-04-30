<?php
namespace Database\SQL\Parts;

use Basic\Arr;

class OrderBy extends Internal\ArrayPartBase {
	function _Set($k,$order_by){
		if($k === null || \Basic\String\Number::is($k)){
			if(is_string($order_by)){
				$this->data[] = $order_by;
			}elseif($order_by instanceof OrderBy){
				//Add(OrderByPart)
				$this->order_by = $order_by;
			}elseif(is_array($order_by)){
				if(Arr::is_assoc($order_by)){
					foreach($order_by as $key=>$order){
						$this->_Set($key,$order);
					}
				}else{
					foreach($order_by as $o){
						if(is_array($o)){
							//Add(array(array(expr1,order1 = ASC),array(expr1,order2 = ASC)))
							if(count($o) == 2){
								$this->_Add($o[0], $o[1]);
							}else{
								throw new \Exception('Unknown array format');
							}
						}else{
							//Add(array(expr1,expr2))
							$this->_Add(null,$o);
						}
					}
				}
				if(count($order_by[0]) == 2){
					$this->order_by = new OrderBy($order_by[0],$order_by[1]);
				}else{
					throw new \Exception('Invalid Order By call array');
				}
			}elseif(func_get_arg(1) !== false){
				$args = func_get_args();
				return $this->order_by($args);
			}else{
				throw new \Exception('Invalid order by call');
			}
		}else{
			//Add(expr,order)
			$this->order_by[] = new OrderByPart($k,$v);
		}
	}
	function toSQL(){
		$ret = implode(', ',$this->data);
		if($ret){
			$ret = 'ORDER BY '.$ret;
		}
		return $ret;
	}
}