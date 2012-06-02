<?php
namespace Database\SQL\Internal;

use Database\SQL\IStatement;

abstract class StatementBase extends MergeBase implements IStatement {
	function Execute(){
		$sql = $this->toSQL();
		return \DB::Q($sql);
	}
	function Query(){
		return $this->Execute();
	}
	function __toString(){
		return $this->toSQL();
	}
}