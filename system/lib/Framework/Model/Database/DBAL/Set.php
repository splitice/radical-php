<?php
namespace Model\Database\DBAL;

class Set extends \Basic\Arr\Object\ArrayObject {
	function orderBy($field,$order='ASC'){
		$method = 'get'.ucfirst($field);
		if(strtoupper($order) == 'ASC'){
			$order = 1;
		}else{
			$order = -1;
		}
		usort($this->data, function($a,$b) use($method,$order){
			$a = $a->$method();
			$b = $b->$method();
			
			//TODO: understanding
			//$at = strtotime($a);
			//$bt = strtotime($b);
			
			if($a>$b){
				return 1*$order;
			}
			if($a<$b){
				return -1*$order;
			}
			return 0;
		});

		return $this;
	}
	function limit($offset,$limit = null){
		if($limit === null){
			$limit = $offset;
			$offset = 0;
		}
		$this->data = array_slice($this->data, $offset, $limit);
		return $this;
	}
}