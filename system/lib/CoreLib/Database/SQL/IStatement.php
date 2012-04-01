<?php
namespace Database\SQL;

use Database\IToSQL;

interface IStatement extends IToSQL {
	function mergeTo(IStatement $mergeIn);
	function _mergeSet(array $import);
}