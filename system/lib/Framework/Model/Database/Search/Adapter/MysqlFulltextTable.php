<?php
namespace Model\Database\Search\Adapter;

use Model\Database\SQL\Parts\Expression\Comparison;

use Model\Database\SQL\Parts\Expression\In;
use Model\Database\SQL\Parts\WhereOR;
use Model\Database\SQL\Parts\Where;
use Model\Database\Model\DynamicTableReference;
use Model\Database\Model\TableReference;
use Model\Database\Model\TableReferenceInstance;
use Model\Database\SQL\SelectStatement;

class MysqlFulltextTable extends MysqlFulltext {
	private function _myisamName(TableReferenceInstance $table){
		return 'search_'.md5($table->getName());
	}
	function _fields(){
		return $this->fields;
	}
	private function _createTable(TableReferenceInstance $table){
		$myisam = $this->_myisamName($table);
		$mTable = new DynamicTableReference($myisam,'search_');
		$mysqlInstance = $this;
		$cT = $mTable->Definition(function($cTable) use($mysqlInstance,$table) {
			$orm = $table->getORM();
			foreach($orm->id as $id){
				$cTable->addField($id,'INT UNSIGNED');
			}
			$cTable->addIndex('PRIMARY',$orm->id);
			foreach($mysqlInstance->_fields() as $field){
				$cTable->addField($field,'TEXT');
			}
			$cTable->addIndex('FULLTEXT',$mysqlInstance->_fields());
			
			$cTable->select(new SelectStatement($table->getTable(),array_keys($cTable->fields())));
		});
		if($cT){
			$fields = array_keys($cT->fields());
			$this->_triggerCreate($myisam, $table->getTable(), $fields);
		}
		return $mTable;
	}
	private function _triggerCreate($myisam,$innodb,$fields){
		$sql = 'create trigger '.$myisam.' after insert on '.$innodb;
		$sql .= ' FOR EACH ROW insert into '.$myisam.' VALUES(';
		foreach($fields as $f){
			$sql .= 'new.'.$f.',';
		}
		$sql = substr($sql,0,-1);
		$sql .= ')';
		
		\DB::Q($sql);
	}
	function filter($text, SelectStatement $sql, $table){
		$table = TableReference::getByTableName($table);
		if($table === null){
			throw new \Exception('Couldnt find table model "'.$table.'" to search');
		}
		
		$where = new WhereOR();
		$useWhere = false;
		$k = null;
		$i = array();
		$rows = $this->Search($text,$table);
		foreach($rows as $row){
			if(count($row) == 1){
				$a = array_keys($row->toArray());
				$b = array_values($row->toArray());
				$k = $a[0];
				$i[] = $b[0];
			}else{
				$where->Add(new Where($row));
				$useWhere = true;
			}
		}
		
		if($useWhere){
			$sql->where_and($where);
		}else{
			$in = new In($i);
			$in = new Comparison($k, $in, '');
			$sql->where_and($in);
		}
	}
	function search($text, TableReferenceInstance $table){
		$mTable = $this->_createTable($table);
		$ret = parent::Search($text, $mTable);
		return $ret;
	}
}