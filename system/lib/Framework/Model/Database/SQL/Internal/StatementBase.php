<?php
namespace Model\Database\SQL\Internal;

use Model\Database\SQL\IStatement;

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