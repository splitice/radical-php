<?php
namespace Model\Database\SQL;

class FunctionalSelectStatement extends SelectStatement {
	function sum($field,$name = null){
		if($name === null)
			$name = $field;
		
		$this->fields[] = 'SUM('.$name.') as '.$field;
		
		return $this;
	}
	function min($field,$name = null){
		if($name === null)
			$name = $field;
	
		$this->fields[] = 'MIN('.$name.') as '.$field;
	
		return $this;
	}
	function max($field,$name = null){
		if($name === null)
			$name = $field;
	
		$this->fields[] = 'MAX('.$name.') as '.$field;
	
		return $this;
	}
}