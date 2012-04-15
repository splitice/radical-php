<?php
namespace Database\Search\Adapter;

use Database\SQL\Parts\In;

use Database\SQL\Parts\WhereOR;

use Database\SQL\Parts\Where;

use Database\Model\DynamicTableReference;
use Database\Model\TableReference;
use Database\Model\TableReferenceInstance;
use Database\SQL\SelectStatement;

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
	function Filter($text, SelectStatement $sql){
		$from = $sql->from();
		$table = TableReference::getByTableName($from);
		if($table === null){
			throw new \Exception('Couldnt find table model to search');
		}
		
		$where = new WhereOR();
		$useWhere = false;
		$k = null;
		$i = array();
		$rows = $this->Search($text,$table);
		foreach($rows as $row){
			if(count($row) == 1){
				$a = array_keys($row->asArray());
				$b = array_values($row->asArray());
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
			$in = new In($k,$i);
			$sql->where_and($in);
		}
	}
	function Search($text, TableReferenceInstance $table){
		$mTable = $this->_createTable($table);
		$ret = parent::Search($text, $mTable);
		return $ret;
	}
}