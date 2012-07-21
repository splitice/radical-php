<?php
namespace Model\Database\SQL;

interface IMergeStatement {
	function mergeTo(IStatement $mergeIn);
	function _mergeSet(array $in);
}