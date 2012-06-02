<?php
namespace Model\Database\SQL;

use Model\Database\IToSQL;

interface IStatement extends IToSQL {
	function mergeTo(IStatement $mergeIn);
	function _mergeSet(array $import);
}