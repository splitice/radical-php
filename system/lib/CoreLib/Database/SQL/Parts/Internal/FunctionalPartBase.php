<?php
namespace Database\SQL\Parts\Internal;

abstract class FunctionalPartBase extends ArrayPartBase {
	const PART_NAME = '';
	
	function toSQL(){
		if(count($this->data) == 0){
			return 'FALSE';
		}
		return static::PART_NAME.'('.\DB::A($this->data).')';
	}
}