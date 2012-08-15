<?php
namespace Model\Database\SQL;

class FunctionalSelectStatement extends SelectStatement {
	function sum($field,$name = null){
		if($name === null)
			$name = $field;
		
		$this->fields[] = 'SUM('.$name.') as '.$field;
		
		return $this;
	}
	
}