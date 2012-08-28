<?php
namespace Model\Database\Search\Adapter;

use Model\Database\Model\TableReferenceInstance;
use Model\Database\Model\ITable;
use Model\Database\SQL\SelectStatement;

interface ISearchAdapter {
	function filter($text, SelectStatement $sql, $table);
	function search($text, TableReferenceInstance $table);
}