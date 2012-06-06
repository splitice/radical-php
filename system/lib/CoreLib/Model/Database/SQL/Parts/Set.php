<?php
namespace Model\Database\SQL\Parts;

class Set extends Internal\FilterPartBase {		
	function toSQL(){
		if(count($this->data)){
			return 'SET '.\DB::A(array_map(function($r){
				return $r->expr();
			},$this->data));//Ugly hack
		}
	}
}