<?php
namespace Model\Database\SQL\Internal;

use Model\Database\SQL\IStatement;

abstract class StatementBase extends MergeBase implements IStatement {
	function execute(){
		$sql = $this->toSQL();
		return \DB::Q($sql);
	}
	function query(){
		return $this->Execute();
	}
	function __toString(){
		return $this->toSQL();
	}
}