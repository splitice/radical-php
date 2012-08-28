<?php
namespace Model\Database\Search\Adapter;

use Model\Database\Model\TableReferenceInstance;
use Model\Database\SQL\SelectStatement;
use Model\Database\SQL\Parts\Expression\Fulltext;

class MysqlFulltext implements ISearchAdapter {
	const MODE_STANDARD = 0;
	const MODE_BOOLEAN = 1;
	const MODE_NATURAL = 2;
	
	protected $fields;
	protected $mode;
	function __construct($fields,$mode = self::MODE_STANDARD){
		$this->fields = $fields;
		$this->mode = $mode;
	}
	private function isBoolean(){
		switch($this->mode){
			case self::MODE_BOOLEAN:
			case self::MODE_STANDARD:
				return true;
		}
		return false;
	}
	protected function _Filter($text, SelectStatement $sql){
		$ft = new Fulltext($text, $this->fields, $this->isBoolean());
		$sql->where_and($ft);
	}
	function filter($text, SelectStatement $sql, $table){
		return $this->_Filter($text, $sql);
	}
	function search($text, TableReferenceInstance $table){
		$orm = $table->getORM();
		$sql = new SelectStatement($table->getTable(),$orm->id);
		$this->_Filter($text, $sql);
		$res = \DB::Q($sql);
		$rows = $res->FetchAll();
		return $rows;
	}
}